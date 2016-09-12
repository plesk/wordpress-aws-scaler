#!/bin/bash

# TODOS
# - delete EBS volumnes with delete command: aws ec2 delete-volume --volume-id vol-xxxxxxx
# - create, list and delete autoscaling groups
# - create, list and delete CloudFront
# - create, list and delete CloudWatch Alarms
# - handover settings / DB values to EC2 User Data


# Function to load values from the configuration file
# Parameters: 1 Name of the Key / 2 Default value (optional)
function get_config
{
	while read -r a b; do
		if [[ $1 == $a ]]
		then
			regex="^\"(.*)\"$"
			if [[ $b =~ $regex ]]
			then
				echo ${BASH_REMATCH[1]}
				set="true"
				break
            else
                if [[ -n $2 ]]
                then
                    echo $2
                    set="true"
                    break
                fi
            fi
		fi
	done < manage_wordpress.ini

	if [[ -z $set ]]
    then
        if [[ -n $2 ]]
        then
            echo $2
        fi
    fi
}

function parse_json
{
    regex="\"${1}\":[[:space:]]?\"?([^,}\"]*)\"?(,|[[:space:]]\})"
 
    if [[ $2 =~ $regex ]]; then
        echo ${BASH_REMATCH[1]}
    else
        echo ''
    fi
}

# Function to get a specific value within a specified block, by using a key / pair search
# Parameters: 1 Content string / 2 Key within same block / 3 Key to find / 4 Value to match
function block_search
{
    if [[ -n ${3} ]] && [[ -n ${4} ]]
    then
        regex="(\{[^\{]*\"${3}\":[[:space:]]?\"?${4}\"?[^}]*\})"
    else
        regex="(.*)"
    fi

    if [[ $1 =~ $regex ]]
    then
        block_data=${BASH_REMATCH[1]}

        if [[ -n ${3} ]] && [[ -n ${4} ]]
        then
            regex_sub_block="\}.*\"${3}\":[[:space:]]?\"?${4}\"?(.*\{)?"

            if [[ $block_data =~ $regex_sub_block ]]
            then
                regex="(\{[^{]*\{[^{]*\"${3}\":[[:space:]]?\"?${4}\"?[^}]*\}[^}]*\})"

                if [[ $1 =~ $regex ]]
                then
                    block_data=${BASH_REMATCH[1]}
                fi
            fi
        fi

        regex_block="\"${2}\":[[:space:]]?\"?([^,}\"]*)\"?(,|[[:space:]]*\})"
   
        if [[ $block_data =~ $regex_block ]]
        then
            echo ${BASH_REMATCH[1]}
        fi
    fi
}
 
# Function to get an array of specific values within a specified block, by using a key / pair search
# Parameters: 1 Content string / 2 Key within same block / 3 Key to find / 4 Value to match
function block_search_array
{
    search_array=()
 
    block_search_content=${1}
    block_search_result_key=${2}
    block_search_key=${3}
    block_search_value=${4}

    var_block=$(block_search "$block_search_content" "$block_search_result_key" "$block_search_key" "$block_search_value")
 
    while [ -n "$var_block" ]; do
        search_array[index++]="$var_block"
        block_search_content="${block_search_content/$block_search_value/}"
        var_block=$(block_search "$block_search_content" "$block_search_result_key" "$block_search_key" "$block_search_value")
    done
}

# Set values from configuration file
SEC_GROUP_NAME=$(get_config SEC_GROUP_NAME "WordPressScalerSecurityGroup")
SEC_GROUP_DESC=$(get_config SEC_GROUP_DESC "Security Group for Plesk WordPress Scaler")
TAG=$(get_config TAG "WordPressScaler")
AMI=$(get_config AMI "ami-64385917")
DEVICE_NAME=$(get_config DEVICE_NAME "WordPressScalerDisk")
INSTANCE_TYPE=$(get_config INSTANCE_TYPE "m3.medium")
REGION=$(get_config REGION "eu-west-1")
VPC_IP_BLOCK=$(get_config VPC_IP_BLOCK "172.31.0.0/16")
DB_INSTANCE_TYPE=$(get_config DB_INSTANCE_TYPE "db.t2.small")
DB_NAME=$(get_config DB_NAME "wordpressscaler")
DB_PASSWORD=$(get_config DB_PASSWORD)
DB_USERNAME=$(get_config DB_USERNAME "wordpress")
DB_ENGINE=$(get_config DB_ENGINE "mariadb")
ELB_NAME=$(get_config ELB_NAME "WordPressScaler")
S3_BUCKET_NAME=$(get_config S3_BUCKET_NAME "WordPressScaler")

# Other settings
LOG_FILE="manage_wordpress.log"

# Command line parameters
ACTION=$1
PARAM=$2

echo
echo "##################################"
echo "# Plesk WordPress Scaler for AWS #"
echo "##################################"
echo 

OUTPUT=$(aws --version 2>&1 > /dev/null)
if [[ ! $OUTPUT == "aws-cli"* ]]; then
	echo "IMPORTANT: Please install AWS CLI first: https://docs.aws.amazon.com/cli/latest/userguide/installing.html"
    echo 
    echo "It's Open Source! Contribute at https://github.com/plesk/wordpress-aws-scaler"
    echo "Follow us at https://twitter.com/PleskOfficial"
	exit
fi

# Create a new WordPress in your AWS account via AWS CLI.
if [[ $ACTION == "create" ]]; then
    echo "----- CREATE NEW WORDPRESS -----" >> "$LOG_FILE"
    
    echo "[1/6] Check VPC..."
    OUTPUT=$(aws ec2 describe-vpcs)
    # "VpcId": "vpc-fffbe19a",
    # "IsDefault": true
 
    VPC_ID=$(block_search "$OUTPUT" "VpcId" "IsDefault" "true")
    if [[ -z $VPC_ID ]]; then
        echo "   Creating VPC..."
        # sample OUTPUT: { "Vpc": { "VpcId": "vpc-xxxxxxxx", "InstanceTenancy": "default", "State": "pending", "DhcpOptionsId": "dopt-xxxxxxxx", "CidrBlock": "172.31.0.0/16", "IsDefault": false } }    
        CMD="aws ec2 create-vpc --cidr-block $VPC_IP_BLOCK"
    	echo "$CMD" >> "$LOG_FILE"
    	OUTPUT=$($CMD)
        echo "$OUTPUT" >> "$LOG_FILE"
        VPC_ID=$(parse_json VpcId "$OUTPUT")
    fi    
    echo "   VPC ID: $VPC_ID"

    echo "[2/6] Check Security Group..."
    OUTPUT=$(aws ec2 describe-security-groups)

    SEC_GROUP_ID=$(block_search "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -z $SEC_GROUP_ID ]]; then
        echo "   Creating Security Group..."
        # sample OUTPUT: { "GroupId": "sg-xxxxxxxxx" }       
        CMD="aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description \"$SEC_GROUP_DESC\" --vpc-id $VPC_ID"
    	echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description "$SEC_GROUP_DESC" --vpc-id $VPC_ID)
    	echo "$OUTPUT" >> "$LOG_FILE"
        SEC_GROUP_ID=$(parse_json GroupId "$OUTPUT")    
    fi
    echo "   Security Group Id: $SEC_GROUP_ID"        
       
    echo "[3/6] Check RDS Database..."
    OUTPUT=$(aws rds describe-db-instances)

    DB=$(block_search "$OUTPUT" "DBInstanceIdentifier" "DBInstanceIdentifier" "$DB_NAME")
    if [[ -z $DB ]]; then
        echo "   Creating RDS Database..."
        CMD="aws rds create-db-instance --engine $DB_ENGINE --db-instance-class $DB_INSTANCE_TYPE --db-instance-identifier $DB_NAME --db-name $DB_NAME --master-user-password $DB_PASSWORD --master-username $DB_USERNAME --allocated-storage 5 --vpc-security-group-ids $SEC_GROUP_ID --tags Key=Name,Value=$TAG"
    	echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws rds create-db-instance --engine $DB_ENGINE --db-instance-class $DB_INSTANCE_TYPE --db-instance-identifier $DB_NAME --db-name $DB_NAME --master-user-password $DB_PASSWORD --master-username $DB_USERNAME --allocated-storage 5 --vpc-security-group-ids $SEC_GROUP_ID --tags "Key=Name,Value=$TAG")
        # --db-security-groups <value>
        # --vpc-security-group-ids <value>
        # --license-model general-public-license
        # [--publicly-accessible | --no-publicly-accessible]
        # [--storage-type <value>]
    	echo "$OUTPUT" >> "$LOG_FILE"
        DB=$(parse_json "DBInstanceIdentifier" "$OUTPUT")    
    fi
    echo "   RDS Database: $DB"        

    echo "[4/6] Check S3 Bucket..."
    OUTPUT=$(aws s3api list-buckets)

    S3_BUCKET=$(block_search "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
    if [[ -z $S3_BUCKET ]]; then
        echo "   Creating S3 Bucket..."
        CMD="aws s3api create-bucket --bucket $S3_BUCKET_NAME --region $REGION --create-bucket-configuration LocationConstraint=$REGION --output text"
    	echo "$CMD" >> "$LOG_FILE"
    	OUTPUT=$($CMD)
    	echo "$OUTPUT" >> "$LOG_FILE"
        S3_BUCKET=$(parse_json "Name" "$OUTPUT")    
        aws s3api put-bucket-tagging --bucket $S3_BUCKET_NAME --tagging TagSet="[{Key=Name,Value=$TAG}]"
    fi
    echo "   S3 Storage: $S3_BUCKET"        
        
    # TODO Check and create ELB
    echo "[5/6] Check Elastic Loadbalancer..."
    OUTPUT=$(aws elb describe-load-balancers)

    ELB=$(block_search "$OUTPUT" "LoadBalancerName" "LoadBalancerName" "$ELB_NAME")
    if [[ -z $ELB ]]; then    
    	echo "   Creating ELB"
   	 	CMD="aws elb create-load-balancer --load-balancer-name $ELB_NAME --listeners \"Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80\" --security-groups $SEC_GROUP_ID --availability-zones eu-west-1a eu-west-1b eu-west-1c"
      	echo "$CMD" >> "$LOG_FILE"
   	 	OUTPUT=$(aws elb create-load-balancer --load-balancer-name $ELB_NAME --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" --security-groups $SEC_GROUP_ID --availability-zones eu-west-1a eu-west-1b eu-west-1c)
    	echo "$OUTPUT" >> "$LOG_FILE"
        ELB=$(parse_json "DNSName" "$OUTPUT")    
    	aws elb add-tags --load-balancer-name $ELB_NAME --tags "Key=Name,Value=$TAG"
    fi
    echo "   Elastic Loadbalancer: $ELB"        

    # TODO Check and create AutoScalingGroups 
    # TODO Check and create CloudFront         
    # TODO Check and create Alarms                                 
               
    echo "[5/6] Creating EC2 instances..."        
    CMD="aws ec2 run-instances --image-id $AMI --instance-type $INSTANCE_TYPE --count 2 --security-group-ids $SEC_GROUP_ID --region $REGION --block-device-mappings [{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}] --cli-input-json file://ec2-config.json"
  	echo "$CMD" >> "$LOG_FILE"
    OUTPUT=$(aws ec2 run-instances --image-id $AMI --instance-type $INSTANCE_TYPE --count 2 --security-group-ids $SEC_GROUP_ID --region $REGION --block-device-mappings "[{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}]" --cli-input-json file://ec2-config.json)
    # --count 2
    # --subnet-id subnet-xxxxxxxx
    # --block-device-mappings "[{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":false}}]"
    # "InstanceId": "i-xxxxxxxx"
    echo "$OUTPUT" >> "$LOG_FILE"

    block_search_array "$OUTPUT" "InstanceId" "ImageId" "$AMI"
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "   Adding tag $TAG to new EC2 Instance Id: $INSTANCE_ID"
        OUTPUT2=$(aws ec2 create-tags --resources $INSTANCE_ID --tags Key=Name,Value=$TAG)
    done 

    times=0
    echo
    while [ 5 -gt $times ] && [[ -z $(aws ec2 describe-instances --instance-id $INSTANCE_ID | grep "running") ]]
    do
        times=$(( $times + 1 ))
        echo Attempt $times at verifying $INSTANCE_ID is running...
        sleep 5s
    done

    echo

    if [ 5 -eq $times ]; then
        echo Instance $INSTANCE_ID is not running. Exiting...
        exit    
    fi

    echo 
    echo "WordPress instances up and running."   
    echo 

# Delete the WordPress incl. database etc BE CAREFUL - this deletes all that the script created before.
elif [[ $ACTION == "delete" ]]; then
    if ! [[ $PARAM == "OK" ]]; then
        echo -e "Please enter \"OK\" to delete all existing WordPress instances: \c "
        read  PARAM
    fi
    
    if [ $PARAM = "OK" ]; then

        echo "----- DELETE WORDPRESS -----" >> "$LOG_FILE"
        
        echo "[1/6] Searching EC2 instances..."
        OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
 
 		i=0
        block_search_array "$OUTPUT" "InstanceId" "ImageId" "$AMI"
        for INSTANCE_ID in "${search_array[@]}"
        do
	        echo "   Deleting EC2 Instance $INSTANCE_ID..."
	        CMD="aws ec2 terminate-instances --instance-ids $INSTANCE_ID"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
            i=$((i+1))
	    done 
 		echo "   $i EC2 Instances deleted"  

        echo "[2/6] Searching RDS Database..."
        OUTPUT=$(aws rds describe-db-instances)

        DB=$(block_search "$OUTPUT" "DBInstanceIdentifier" "DBInstanceIdentifier" "$DB_NAME")
        if [[ -n $DB ]]; then
            echo "   Deleting RDS Database $DB..."
            CMD="aws rds delete-db-instance --db-instance-identifier $DB --skip-final-snapshot"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
        else
            echo "   No RDS Database found"        
        fi

        echo "[3/6] Searching S3 Bucket..."
        OUTPUT=$(aws s3api list-buckets)

        S3_BUCKET=$(block_search "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
        if [[ -n $S3_BUCKET ]]; then
            echo "   Deleting S3 Bucket $S3_BUCKET..."
            CMD="aws s3api delete-bucket --bucket $S3_BUCKET"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
        else
            echo "   No S3 Bucket found"        
        fi


        echo "[5/6] Searching Elastic Loadbalancer..."
    	OUTPUT=$(aws elb describe-load-balancers)

    	ELB=$(block_search "$OUTPUT" "LoadBalancerName" "LoadBalancerName" "$ELB_NAME")
    	if [[ -n $ELB ]]; then    
		    echo "   Deleting Elastic Loadbalancer $ELB..."     
            CMD="aws elb delete-load-balancer --load-balancer-name $ELB"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
		else
            echo "   No Elastic Loadbalancer found"        
		fi

        echo "[5/6] Searching Security Group..."
        OUTPUT=$(aws ec2 describe-security-groups)

        SEC_GROUP_ID=$(block_search "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
        if [[ -n $SEC_GROUP_ID ]]; then
            echo "   Deleting Security Group $SEC_GROUP_ID..."
            CMD="aws ec2 delete-security-group --group-id $SEC_GROUP_ID"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
        else
            echo "   No Security Group found"        
        fi 

        echo 
        echo "WordPress and all depending data deleted."           
        echo
    fi

# List settings and WordPress instances.
elif [[ $1 == "list" ]]; then

    echo "Settings"
    echo "------------------------------------------------------"
    echo "EC2 Instances"
    echo "   Instance Type:              $INSTANCE_TYPE"
    echo "   Region:                     $REGION"
    echo "   Amazon Machine Image Id:    $AMI"
    echo "   Device Name:                $DEVICE_NAME"
    echo "   Security Group Name:        $SEC_GROUP_NAME"
    echo "   Security Group Description: $SEC_GROUP_DESC"
    echo "   VPC IP Block:               $VPC_IP_BLOCK "
    echo "   Tag:                        $TAG "
    echo
    echo "Elastic Loadbalancer"
    echo "   ELB Name:                   $ELB_NAME"
    echo
    echo "RDS Database"
    echo "   DB Engine:                  $DB_ENGINE"
    echo "   DB Instance Type:           $DB_INSTANCE_TYPE"
    echo "   DB Name:                    $DB_NAME"
    echo "   DB User:                    $DB_USERNAME"
    echo "   DB Password:                $DB_PASSWORD"
    echo
    echo "S3 Storage"
    echo "   S3 Bucket:                  $S3_BUCKET_NAME"
    echo
    echo "Resources"
    echo "------------------------------------------------------"

    echo "Searching Security Groups..."
    OUTPUT=$(aws ec2 describe-security-groups)

    # "GroupName": "WordPressScalerSecurityGroup",
    # "GroupId": "sg-xxxxxxxx"    
    # "VpcId": "vpc-xxxxxxxx"
    # or returns "InvalidGroup.NotFound" if no security group found

    VPC_ID=$(block_search "$OUTPUT" "VpcId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -n $VPC_ID ]]; then
	    echo "   VPC ID: $VPC_ID"
    fi

    SEC_GROUP_ID=$(block_search "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -n $SEC_GROUP_ID ]]; then
	    echo "   Security Group: $SEC_GROUP_ID"
	else
	    echo "   Security Group: none"
    fi
     
    echo
    echo "Searching S3 buckets..."
    OUTPUT=$(aws s3api list-buckets)

    S3_BUCKET=$(block_search "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
    if [[ -n $S3_BUCKET ]]; then
	    echo "   S3 Storage: $S3_BUCKET"
	else
	    echo "   S3 Storage: none"
    fi 
     
    echo
    echo "Searching RDS instances..."
    OUTPUT=$(aws rds describe-db-instances)

    DB=$(block_search "$OUTPUT" "DBInstanceIdentifier" "DBInstanceIdentifier" "$DB_NAME")
    if [[ -n $DB ]]; then
	    echo "   RDS Database: $DB"
	else
	    echo "   RDS Database: none"
    fi

    echo
    echo "Searching Elastic Loadbalancers..."     
    OUTPUT=$(aws elb describe-load-balancers)

    ELB=$(block_search "$OUTPUT" "DNSName" "LoadBalancerName" "$ELB_NAME")
    if [[ -n $ELB ]]; then    
    	echo "   Elastic Loadbalancer: $ELB" 
    else   
    	echo "   Elastic Loadbalancer: none"    
    fi
    
    echo
    echo "Searching EC2 instances..."
    OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
 
    i=0
    block_search_array "$OUTPUT" "InstanceId" "ImageId" "$AMI"
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "   EC2 Instance $INSTANCE_ID"
	    i=$((i+1))
    done    
    
	echo "   $i EC2 Instances found"  
	echo 
    
# Help page        
else 
    echo "Plesk WordPress Scaler automates provisioning of a highly available and auto-scaling WordPress to AWS."
    echo
    echo "Commands:"
    echo "   manage_wordpress.sh create        Create a new WordPress in your AWS account via AWS CLI."
    echo "   manage_wordpress.sh delete        Delete the WordPress incl. database etc BE CAREFUL - this deletes all that the script created before."
    echo "   manage_wordpress.sh list          List settings and WordPress instances."
    echo 
    echo "It's Open Source! Contribute at https://github.com/plesk/wordpress-aws-scaler"
    echo "Follow us at https://twitter.com/PleskOfficial"
    echo
fi
