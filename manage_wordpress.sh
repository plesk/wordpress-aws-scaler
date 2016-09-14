#!/bin/bash

# TODOS
# - create, list and delete autoscaling groups
# - create, list and delete CloudFront
# - create, list and delete CloudWatch Alarms
# - check EC2 state for "running", "shutting-down", "terminated", "pending"

# ----------- FUNCTIONS -----------
# Function to load values from the configuration file
# Parameters: 1 File / 2 Name of the Key / 3 Default value (optional)
function get_config
{
	if [ -f "$1.ini" ]; then
		while read -r a b; do
			if [[ $2 == $a ]]
			then
				regex="^\"(.*)\"$"
				if [[ $b =~ $regex ]]
				then
					echo ${BASH_REMATCH[1]}
					set="true"
					break
	            else
	                if [[ -n $3 ]]
	                then
	                    echo $3
	                    set="true"
	                    break
	                fi
	            fi
			fi
		done < "$1.ini"
	
		if [[ -z $set ]]
	    then
	        if [[ -n $3 ]]
	        then
	            echo $3
	        fi
	    fi
	else
		echo $3
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

# Function to remove invalid characters from identifiers
# Parameters: 1 Identifier
function get_valid_id
{
	ID=$1
	ID=${ID//_}
	ID=$(echo $ID | tr '[:upper:]' '[:lower:]')
	echo $ID
}

# Function to generate the s3 url for a given s3 bucket name
# Parameters: 1 S3 Bucket Name
function get_s3_url
{
	prefix="https://"
	suffix=".s3.amazonaws.com/"
	echo $prefix$1$suffix
}

function print_footer
{
    echo 
    echo "It's Open Source! Contribute at https://github.com/plesk/wordpress-aws-scaler"
    echo "Follow us at https://twitter.com/PleskOfficial"
    echo
}

# ----------- CHECK AND CONFIGURE -----------
# Command line parameters
ACTION=$1
if [[ $3 == "OK" ]]; then
	OK="OK"
fi
if [[ -n $2 ]]; then
	if [[ $2 == "OK" ]]; then
		OK="OK"
	else
		TAG=$2
	fi
fi

echo
echo "##################################"
echo "# Plesk WordPress Scaler for AWS #"
echo "##################################"
echo 

# pre-checks
OUTPUT=$(aws --version 2>&1 > /dev/null)
if [[ ! $OUTPUT == "aws-cli"* ]]; then
	echo "IMPORTANT: Please install AWS CLI first: https://docs.aws.amazon.com/cli/latest/userguide/installing.html"
	print_footer
	exit
fi

# if TAG was not passed from CLI, fetch it from settings file or use default
if [[ -z $TAG ]]; then
	TAG=$(get_config "manage_wordpress" TAG "pleskwp")
fi

TAG=$(get_valid_id "$TAG")

if ! [[ $TAG =~ ^[a-z0-9]*$ ]]; then
	echo "IMPORTANT: The TAG \"$TAG\" must only contain small letters or numbers."
	print_footer
	exit
fi

# Other settings
LOG_FILE="$TAG.log"

# Set values from configuration file
AMI=$(get_config "$TAG" AMI "ami-64385917")
INSTANCE_TYPE=$(get_config "$TAG" INSTANCE_TYPE "m3.medium")
REGION=$(get_config "$TAG" REGION "eu-west-1")
VPC_IP_BLOCK=$(get_config "$TAG" VPC_IP_BLOCK "172.31.0.0/16")
DB_INSTANCE_TYPE=$(get_config "$TAG" DB_INSTANCE_TYPE "db.t2.small")
DB_PASSWORD=$(get_config "$TAG" DB_PASSWORD "changeme")
DB_USERNAME=$(get_config "$TAG" DB_USERNAME "wordpress")
DB_ENGINE=$(get_config "$TAG" DB_ENGINE "mariadb")
EC2_MIN_INSTANCES=$(get_config "$TAG" EC2_MIN_INSTANCES "2")
EC2_MAX_INSTANCES=$(get_config "$TAG" EC2_MAX_INSTANCES "20")

SEC_GROUP_NAME=$(get_config "$TAG" SEC_GROUP_NAME "$TAG")
SEC_GROUP_DESC=$(get_config "$TAG" SEC_GROUP_DESC "Security Group for Plesk WordPress Scaler")
DEVICE_NAME=$(get_config "$TAG" DEVICE_NAME "$TAG")
DB_NAME=$(get_config "$TAG" DB_NAME "$TAG")
ELB_NAME=$(get_config "$TAG" ELB_NAME "$TAG")
ASG_NAME=$(get_config "$TAG" ASG_NAME "$TAG")
LC_NAME=$(get_config "$TAG" LC_NAME "$TAG")
S3_BUCKET_NAME=$(get_config "$TAG" S3_BUCKET_NAME "$TAG")

# remove _ characters in names
DB_USERNAME=$(get_valid_id "$DB_USERNAME")
DB_NAME=$(get_valid_id "$DB_NAME")
ELB_NAME=$(get_valid_id "$ELB_NAME")
S3_BUCKET_NAME=$(get_valid_id "$S3_BUCKET_NAME")

# ----------- CREATE -----------
# Create a new WordPress in your AWS account via AWS CLI.
if [[ $ACTION == "create" ]]; then
    echo "----- CREATE NEW WORDPRESS -----" >> "$LOG_FILE"
    STEP=0
    STEPS=9
    
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check VPC..."
    OUTPUT=$(aws ec2 describe-vpcs)
    # "VpcId": "vpc-fffbe19a",
    # "IsDefault": true
 
    VPC_ID=$(block_search "$OUTPUT" "VpcId" "IsDefault" "true")
    if [[ -z $VPC_ID ]]; then
        echo "      Creating VPC..."
        # sample OUTPUT: { "Vpc": { "VpcId": "vpc-xxxxxxxx", "InstanceTenancy": "default", "State": "pending", "DhcpOptionsId": "dopt-xxxxxxxx", "CidrBlock": "172.31.0.0/16", "IsDefault": false } }    
        CMD="aws ec2 create-vpc --cidr-block $VPC_IP_BLOCK"
    	echo "$CMD" >> "$LOG_FILE"
    	OUTPUT=$($CMD)
        echo "$OUTPUT" >> "$LOG_FILE"
        VPC_ID=$(parse_json VpcId "$OUTPUT")
    fi    
    echo "      VPC ID: $VPC_ID"

   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Security Group..."
    OUTPUT=$(aws ec2 describe-security-groups)

    SEC_GROUP_ID=$(block_search "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -z $SEC_GROUP_ID ]]; then
        echo "      Creating Security Group..."
        # sample OUTPUT: { "GroupId": "sg-xxxxxxxxx" }       
        CMD="aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description \"$SEC_GROUP_DESC\" --vpc-id $VPC_ID"
    	echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description "$SEC_GROUP_DESC" --vpc-id $VPC_ID)
    	echo "$OUTPUT" >> "$LOG_FILE"
        SEC_GROUP_ID=$(parse_json GroupId "$OUTPUT")   
        
        echo "      Adding Firewall Rules..."
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 22 --cidr 0.0.0.0/0        
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 80 --cidr 0.0.0.0/0        
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 443 --cidr 0.0.0.0/0        
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 3306 --cidr 0.0.0.0/0                
    fi
    echo "      Security Group Id: $SEC_GROUP_ID"        

   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check S3 Bucket..."
    OUTPUT=$(aws s3api list-buckets)

    S3_BUCKET=$(block_search "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
    if [[ -z $S3_BUCKET ]]; then
        echo "      Creating S3 Bucket..."
        CMD="aws s3api create-bucket --bucket $S3_BUCKET_NAME --region $REGION --create-bucket-configuration LocationConstraint=$REGION"
    	echo "$CMD" >> "$LOG_FILE"
    	OUTPUT=$($CMD)
    	echo "$OUTPUT" >> "$LOG_FILE"
        S3_BUCKET=$(parse_json "Location" "$OUTPUT")    
        if [[ -n $S3_BUCKET ]]; then
        	aws s3api put-bucket-tagging --bucket $S3_BUCKET_NAME --tagging TagSet="[{Key=Name,Value=$TAG}]"
        fi
    fi
    S3_URL=$(get_s3_url $S3_BUCKET_NAME)
    echo "      S3 Storage: $S3_URL"        
               
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check RDS Database..."
    OUTPUT=$(aws rds describe-db-instances --db-instance-identifier $DB_NAME)

    DB=$(block_search "$OUTPUT" "Address" "Port" "3306")
    if [[ -z $DB ]]; then
        echo "      Creating RDS Database..."
        CMD="aws rds create-db-instance --engine $DB_ENGINE --db-instance-class $DB_INSTANCE_TYPE --db-instance-identifier $DB_NAME --db-name $DB_NAME --master-user-password $DB_PASSWORD --master-username $DB_USERNAME --allocated-storage 5 --vpc-security-group-ids $SEC_GROUP_ID --tags Key=Name,Value=$TAG"
    	echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws rds create-db-instance --engine $DB_ENGINE --db-instance-class $DB_INSTANCE_TYPE --db-instance-identifier $DB_NAME --db-name $DB_NAME --master-user-password $DB_PASSWORD --master-username $DB_USERNAME --allocated-storage 5 --vpc-security-group-ids $SEC_GROUP_ID --tags "Key=Name,Value=$TAG")
        # --db-security-groups <value>
        # --vpc-security-group-ids <value>
        # --license-model general-public-license
        # [--publicly-accessible | --no-publicly-accessible]
        # [--storage-type <value>]
    	echo "$OUTPUT" >> "$LOG_FILE"
	    DB=$(parse_json DBInstanceIdentifier "$OUTPUT")
	    
		# we need to wait for the DB to be up and running in order to get the hostname that we need for the WordPress Docker container	    
        echo "      Checking until Database is available and has a dns name. This can take a while..."
	    echo
	    times=0
	    while [ 20 -gt $times ] && [[ -z $(aws rds describe-db-instances --db-instance-identifier $DB_NAME | grep "Address") ]]
	    do
	        times=$(( $times + 1 ))
	        echo Attempt $times at verifying $DB_NAME is running...
	        sleep 10s
	    done
	
	    echo
		
		OUTPUT=$(aws rds describe-db-instances --db-instance-identifier $DB_NAME)
	    DB=$(block_search "$OUTPUT" "Address" "Port" "3306")
	
	    if [[ -z $DB ]]; then
	        echo "      Database $DB_NAME is not running. Please wait a minute and re-run create!"
	        exit    
	    fi
	    
    fi
    echo "      RDS Database: $DB"        

   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Elastic Loadbalancer..."
    OUTPUT=$(aws elb describe-load-balancers)

    ELB=$(block_search "$OUTPUT" "DNSName" "LoadBalancerName" "$ELB_NAME")
    if [[ -z $ELB ]]; then    
    	echo "      Creating ELB"
   	 	CMD="aws elb create-load-balancer --load-balancer-name $ELB_NAME --listeners \"Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80\" --security-groups $SEC_GROUP_ID --availability-zones eu-west-1a eu-west-1b eu-west-1c"
      	echo "$CMD" >> "$LOG_FILE"
   	 	OUTPUT=$(aws elb create-load-balancer --load-balancer-name $ELB_NAME --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" --security-groups $SEC_GROUP_ID --availability-zones eu-west-1a eu-west-1b eu-west-1c)
    	echo "$OUTPUT" >> "$LOG_FILE"
        ELB=$(parse_json "DNSName" "$OUTPUT")    
    	aws elb add-tags --load-balancer-name $ELB_NAME --tags "Key=Name,Value=$TAG"
    fi
    echo "      Elastic Loadbalancer: $ELB"                                     
               
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Generating EC2 User Data script..."                       
    cat >ec2-user-data.sh <<EOL
#!/bin/bash
docker pull janloeffler/wordpress-aws-scaler:latest
docker run -d -p 80:80 -p 443:443 -e WORDPRESS_DB_HOST='${DB}' -e WORDPRESS_DB_USER='${DB_USERNAME}' -e WORDPRESS_DB_PASSWORD='${DB_PASSWORD}' -e WORDPRESS_DB_NAME='${DB_NAME}' -e WORDPRESS_DB_PREFIX='wp_' -e WORDPRESS_URL='http://${ELB}' -it janloeffler/wordpress-aws-scaler:latest
EOL

# parameters to add
# S3_KEY
# S3_SECRET
# S3_BUCKET
# NEWRELIC_KEY
# NEWRELIC_NAME
    
    cat ec2-user-data.sh >> "$LOG_FILE"         

   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Launch Configuration..."
    OUTPUT=$(aws autoscaling describe-launch-configurations)

    LC=$(block_search "$OUTPUT" "LaunchConfigurationName" "LaunchConfigurationName" "$LC_NAME")
    if [[ -z $LC ]]; then    
    	echo "      Creating Launch Configuration"
   	 	CMD="aws autoscaling create-launch-configuration --launch-configuration-name $LC_NAME --image-id $AMI --instance-type $INSTANCE_TYPE --security-groups $SEC_GROUP_ID --block-device-mappings [{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}] --ebs-optimized --user-data file://ec2-user-data.sh"
      	echo "$CMD" >> "$LOG_FILE"
   	 	#OUTPUT=$(aws autoscaling create-launch-configuration --launch-configuration-name $LC_NAME --image-id $AMI --instance-type $INSTANCE_TYPE --security-groups $SEC_GROUP_ID --block-device-mappings "[{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}]" --ebs-optimized --user-data file://ec2-user-data.sh)
   	 	OUTPUT=$(aws autoscaling create-launch-configuration --launch-configuration-name $LC_NAME --image-id $AMI --instance-type $INSTANCE_TYPE --security-groups $SEC_GROUP_ID --user-data file://ec2-user-data.sh)
    	echo "$OUTPUT" >> "$LOG_FILE"
        LC=$(parse_json "LaunchConfigurationName" "$OUTPUT")    
    fi
    echo "      Launch Configuration: $LC" 

   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Auto Scaling Group..."
    OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)

    ASG=$(block_search "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    if [[ -z $ASG ]]; then    
    	echo "      Creating Auto Scaling Group"
   	 	CMD="aws autoscaling create-auto-scaling-group --auto-scaling-group-name $ASG_NAME --launch-configuration-name $LC_NAME --min-size $EC2_MIN_INSTANCES --max-size $EC2_MAX_INSTANCES --load-balancer-names $ELB_NAME --availability-zones eu-west-1a eu-west-1b eu-west-1c --health-check-type ELB --health-check-grace-period 60 --tags Key=Name,Value=$TAG"
      	echo "$CMD" >> "$LOG_FILE"
   	 	OUTPUT=$(aws autoscaling create-auto-scaling-group --auto-scaling-group-name $ASG_NAME --launch-configuration-name $LC_NAME --min-size $EC2_MIN_INSTANCES --max-size $EC2_MAX_INSTANCES --load-balancer-names $ELB_NAME --availability-zones eu-west-1a eu-west-1b eu-west-1c --health-check-type ELB --health-check-grace-period 60 --tags "Key=Name,Value=$TAG")
    	# [--desired-capacity <value>]
    	# [--default-cooldown <value>]
    	# [--target-group-arns <value>]
    	# [--health-check-type <value>]
    	# [--health-check-grace-period <value>]
    	# [--placement-group <value>]
    	# [--vpc-zone-identifier <value>]
    	# [--termination-policies <value>]
    	# [--new-instances-protected-from-scale-in | --no-new-instances-protected-from-scale-in]
    	echo "$OUTPUT" >> "$LOG_FILE"
        ASG=$(parse_json "AutoScalingGroupARN" "$OUTPUT")    
    fi
    echo "      Auto Scaling Group: $ASG" 

	aws autoscaling resume-processes --auto-scaling-group-name $ASG_NAME

    # TODO Check and create CloudFront         
    # TODO Check and create Alarms    
               
   	# STEP=$((STEP+1))
    # echo "[$STEP/$STEPS] Creating EC2 instances..."        
    # CMD="aws ec2 run-instances --image-id $AMI --instance-type $INSTANCE_TYPE --count $EC2_MIN_INSTANCES --security-group-ids $SEC_GROUP_ID --region $REGION --block-device-mappings [{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}] --ebs-optimized --user-data file://ec2-user-data.sh"
  	# echo "$CMD" >> "$LOG_FILE"
    # OUTPUT=$(aws ec2 run-instances --image-id $AMI --instance-type $INSTANCE_TYPE --count $EC2_MIN_INSTANCES --security-group-ids $SEC_GROUP_ID --region $REGION --block-device-mappings "[{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}]" --ebs-optimized --user-data file://ec2-user-data.sh)
    # --subnet-id subnet-xxxxxxxx
    # "InstanceId": "i-xxxxxxxx"
    # echo "$OUTPUT" >> "$LOG_FILE"
	# 
    # block_search_array "$OUTPUT" "InstanceId" "ImageId" "$AMI"
    # for INSTANCE_ID in "${search_array[@]}"
    # do
	#     echo "      Adding tag $TAG to new EC2 Instance Id: $INSTANCE_ID"
    #     OUTPUT2=$(aws ec2 create-tags --resources $INSTANCE_ID --tags Key=Name,Value=$TAG)
    # done 

    OUTPUT=$(aws autoscaling describe-auto-scaling-instances)
 
    i=0
    block_search_array "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "      EC2 Instance: $INSTANCE_ID"
	    i=$((i+1))
    done    

    echo

    times=0
    while [ 5 -gt $times ] && [[ -z $(aws ec2 describe-instances --instance-id $INSTANCE_ID | grep "running") ]]
    do
        times=$(( $times + 1 ))
        echo Check if $INSTANCE_ID is running [$times]...
        sleep 5s
    done

    echo

    if [ 5 -eq $times ]; then
        echo EC2 Instance $INSTANCE_ID is not running. Exiting...
        exit    
    fi

    OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
 
    i=0
    block_search_array "$OUTPUT" "InstanceId" "ImageId" "$AMI"
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "      EC2 Instance: $INSTANCE_ID [running]"
	    i=$((i+1))
    done    

    echo 
    echo "$i WordPress instances up and running."   
    echo 

# ----------- DELETE -----------
# Delete the WordPress incl. database etc BE CAREFUL - this deletes all that the script created before.
elif [[ $ACTION == "delete" ]]; then
    if ! [[ $OK == "OK" ]]; then
        echo -e "Please enter \"OK\" to delete all existing WordPress instances: \c "
        read  OK
    fi
    
    if [[ $OK == "OK" ]]; then

        echo "----- DELETE WORDPRESS -----" >> "$LOG_FILE"
        STEPS=9
        STEP=0

	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Auto Scaling Group..."
    	OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)

    	ASG=$(block_search "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    	if [[ -n $ASG ]]; then    
		    echo "   Setting max instances to 0 for Auto Scaling Group \"$ASG\"..."     
            CMD="aws autoscaling update-auto-scaling-group --auto-scaling-group-name $ASG_NAME --max-size 0 --min-size 0"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
		else
            echo "   No Auto Scaling Group found"        
		fi

	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Auto Scaling Instances..."
    	OUTPUT=$(aws autoscaling describe-auto-scaling-instances)

    	i=0
    	block_search_array "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    	for INSTANCE_ID in "${search_array[@]}"
    	do
	    	# echo "   Detaching Auto Scaling Instance \"$INSTANCE_ID\"..."
	        # CMD="aws autoscaling detach-instances --instance-ids $INSTANCE_ID --auto-scaling-group-name $ASG_NAME --should-decrement-desired-capacity"
    	  	# echo "$CMD" >> "$LOG_FILE"
	    	# OUTPUT=$($CMD)
            # echo "$OUTPUT" >> "$LOG_FILE"

	    	echo "   Deleting Auto Scaling Instance \"$INSTANCE_ID\"..."
	        CMD="aws ec2 terminate-instances --instance-ids $INSTANCE_ID"
	        #CMD="aws autoscaling terminate-instance-in-auto-scaling-group --instance-id $INSTANCE_ID --should-decrement-desired-capacity"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
	   		i=$((i+1))
    	done    
 		echo "   $i Auto Scaling Instances deleted"  

	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Launch Configurations..."
    	OUTPUT=$(aws autoscaling describe-launch-configurations)

	    LC=$(block_search "$OUTPUT" "LaunchConfigurationName" "LaunchConfigurationName" "$LC_NAME")
    	if [[ -n $LC ]]; then    
		    echo "   Deleting Launch Configuration \"$LC\"..."     
            CMD="aws autoscaling delete-launch-configuration --launch-configuration-name $LC_NAME"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
		else
            echo "   No Launch Configurations found"        
		fi

	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching CloudWatch Alarms..."
		# aws cloudwatch delete-alarms --alarm-name AddCapacity RemoveCapacity
		echo "   Not yet implemented"

	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Auto Scaling Group..."
    	OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)

    	ASG=$(block_search "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    	if [[ -n $ASG ]]; then    
		    echo "   Deleting Auto Scaling Group \"$ASG_NAME\"..."     
            CMD="aws autoscaling delete-auto-scaling-group --auto-scaling-group-name $ASG_NAME --force-delete"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
		else
            echo "   No Auto Scaling Group found"        
		fi

	   	# STEP=$((STEP+1))
        # echo "[$STEP/$STEPS] Searching remaining EC2 instances..."
        # OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
 
 		# i=0
        # block_search_array "$OUTPUT" "InstanceId" "ImageId" "$AMI"
        # for INSTANCE_ID in "${search_array[@]}"
        # do
        # 	# check if instance is already terminated
        # 	#"Name": "running"
	    #     #STATE=$(block_search "$OUTPUT" "Name" "InstanceId" "$INSTANCE_ID")
        # 	
	    #    echo "   Deleting EC2 Instance \"$INSTANCE_ID\"..."
	    #     CMD="aws ec2 terminate-instances --instance-ids $INSTANCE_ID"
    	#   	echo "$CMD" >> "$LOG_FILE"
	    # 	OUTPUT=$($CMD)
        #     echo "$OUTPUT" >> "$LOG_FILE"
        #     i=$((i+1))
	    # done 
 		# echo "   $i EC2 Instances deleted"  

	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Elastic Loadbalancer..."
    	OUTPUT=$(aws elb describe-load-balancers)

    	ELB=$(block_search "$OUTPUT" "LoadBalancerName" "LoadBalancerName" "$ELB_NAME")
    	if [[ -n $ELB ]]; then    
		    echo "   Deleting Elastic Loadbalancer \"$ELB\"..."     
            CMD="aws elb delete-load-balancer --load-balancer-name $ELB_NAME"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
		else
            echo "   No Elastic Loadbalancer found"        
		fi

	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching RDS Database..."
        OUTPUT=$(aws rds describe-db-instances --db-instance-identifier $DB_NAME)
        DB=$(block_search "$OUTPUT" "DBInstanceIdentifier" "DBInstanceIdentifier" "$DB_NAME")
        if [[ -n $DB ]]; then
            echo "   Deleting RDS Database \"$DB\"..."
            CMD="aws rds delete-db-instance --db-instance-identifier $DB_NAME --skip-final-snapshot"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
        else
            echo "   No RDS Database found"        
        fi

	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching S3 Bucket..."
        OUTPUT=$(aws s3api list-buckets)

        S3_BUCKET=$(block_search "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
        if [[ -n $S3_BUCKET ]]; then
		    S3_URL=$(get_s3_url $S3_BUCKET_NAME)
            echo "   Deleting S3 Bucket \"$S3_URL\"..."
            CMD="aws s3api delete-bucket --bucket $S3_BUCKET_NAME"
    	  	echo "$CMD" >> "$LOG_FILE"
	    	OUTPUT=$($CMD)
            echo "$OUTPUT" >> "$LOG_FILE"
        else
            echo "   No S3 Bucket found"        
        fi

	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Security Group..."
        OUTPUT=$(aws ec2 describe-security-groups)

        SEC_GROUP_ID=$(block_search "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
        if [[ -n $SEC_GROUP_ID ]]; then
            echo "   Deleting Security Group \"$SEC_GROUP_ID\"..."
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

# ----------- LIST -----------
# List settings and WordPress instances.
elif [[ $1 == "list" ]]; then

    echo "Settings"
    echo "------------------------------------------------------"
    echo "Tag:                           $TAG "
	echo
    echo "Network"
    echo "   Security Group Name:        $SEC_GROUP_NAME"
    echo "   Security Group Description: $SEC_GROUP_DESC"
    echo "   VPC IP Block:               $VPC_IP_BLOCK "
	echo 
    echo "EC2 Instances"
    echo "   Instance Type:              $INSTANCE_TYPE"
    echo "   Region:                     $REGION"
    echo "   Amazon Machine Image Id:    $AMI"
    echo "   Device Name:                $DEVICE_NAME"
    echo
    echo "Elastic Loadbalancer"
    echo "   ELB Name:                   $ELB_NAME"
    echo "   Launch Configuration:       $LC_NAME"
    echo "   Auto Scaling Group:         $ASG_NAME"
    echo "   EC2 Min Instances:          $EC2_MIN_INSTANCES"
    echo "   EC2 Max Instances:          $EC2_MAX_INSTANCES"
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

    OUTPUT=$(aws ec2 describe-security-groups)

    # "GroupName": "WordPressScalerSecurityGroup",
    # "GroupId": "sg-xxxxxxxx"    
    # "VpcId": "vpc-xxxxxxxx"
    # or returns "InvalidGroup.NotFound" if no security group found

    VPC_ID=$(block_search "$OUTPUT" "VpcId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -n $VPC_ID ]]; then
		echo "   VPC ID:                     $VPC_ID"
    fi

    SEC_GROUP_ID=$(block_search "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -n $SEC_GROUP_ID ]]; then
	    echo "   Security Group:             $SEC_GROUP_ID"
	else
	    echo "   Security Group:             none"
    fi
     
    OUTPUT=$(aws s3api list-buckets)

    S3_BUCKET=$(block_search "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
    if [[ -n $S3_BUCKET ]]; then
    	S3_URL=$(get_s3_url $S3_BUCKET_NAME)
	    echo "   S3 Storage:                 $S3_URL"
	else
	    echo "   S3 Storage:                 none"
    fi 
     
    OUTPUT=$(aws rds describe-db-instances --db-instance-identifier $DB_NAME)

    DB=$(block_search "$OUTPUT" "Address" "Port" "3306")
    if [[ -n $DB ]]; then
	    echo "   RDS Database:               $DB"
	else
	    echo "   RDS Database:               none"
    fi

    OUTPUT=$(aws elb describe-load-balancers)

    ELB=$(block_search "$OUTPUT" "DNSName" "LoadBalancerName" "$ELB_NAME")
    if [[ -n $ELB ]]; then    
    	echo "   Elastic Loadbalancer:       $ELB" 
    else   
    	echo "   Elastic Loadbalancer:       none"    
    fi
 
    OUTPUT=$(aws autoscaling describe-launch-configurations)

    LC=$(block_search "$OUTPUT" "LaunchConfigurationName" "LaunchConfigurationName" "$LC_NAME")
    if [[ -n $LC ]]; then    
    	echo "   Launch Configurations:      $LC" 
    else   
    	echo "   Launch Configurations:      none"    
    fi
       
    OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)

    ASG=$(block_search "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    if [[ -n $ASG ]]; then    
    	echo "   Auto Scaling Group:         $ASG" 
    else   
    	echo "   Auto Scaling Group:         none"    
    fi
    
    OUTPUT=$(aws autoscaling describe-auto-scaling-instances)
 
    i=0
    block_search_array "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "   Auto Scaling Instance:      $INSTANCE_ID"
	    i=$((i+1))
    done    
    
	echo "   Auto Scaling Instances:     $i" 
	    
    # OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
    #
    # i=0
    # block_search_array "$OUTPUT" "InstanceId" "ImageId" "$AMI"
    # for INSTANCE_ID in "${search_array[@]}"
    # do
	#     echo "   EC2 Instance:               $INSTANCE_ID"
	#     i=$((i+1))
    # done    
    # 
	# echo "   EC2 Instances:              $i"  
	# echo 
    
# ----------- CONSOLE -----------
# Print console output of first EC2 instance found.
elif [[ $1 == "console" ]]; then

    OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
 
    INSTANCE_ID=$(block_search "$OUTPUT" "InstanceId" "ImageId" "$AMI")
    if [[ -n $INSTANCE_ID ]]; then
	    OUTPUT=$(aws ec2 get-console-output --instance-id $INSTANCE_ID)
	    echo $OUTPUT
	else
	    echo "No EC2 instances found!"
    fi    
    
	echo    
    
# ----------- HELP -----------
# Help page        
else 
    echo "Plesk WordPress Scaler automates provisioning of a highly available and auto-scaling WordPress to AWS."
    echo
    echo "Commands:"
    echo "   manage_wordpress.sh create        Create a new WordPress in your AWS account via AWS CLI."
    echo "   manage_wordpress.sh delete        Delete the WordPress incl. database etc BE CAREFUL - this deletes all that the script created before."
    echo "   manage_wordpress.sh list          List settings and WordPress instances."
    echo "   manage_wordpress.sh console       Print console output of first EC2 instance found."
	print_footer
fi
