#!/bin/bash

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
			fi
        else
            if [[ -n $2 ]]
            then
                echo $2
                break
            fi
		fi
	done < manage_wordpress.ini
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
# Parameters: 1 Key to find / 2 Value to match / 3 Key within same block / 4 Content string
function block_search
{
    regex="(\{[^{]*\"${1}\":[[:space:]]?\"?${2}\"?[^}]*\})"
   
    if [[ $4 =~ $regex ]]
    then
        block_data=${BASH_REMATCH[1]}
        regex_block="\"${3}\":[[:space:]]?\"?([^,}\"]*)\"?(,|[[:space:]]*\})"
   
        if [[ $block_data =~ $regex_block ]]
        then
            echo ${BASH_REMATCH[1]}
        fi
    fi
}
 
# Function to get an array of specific values within a specified block, by using a key / pair search
# Parameters: 1 Key to find / 2 Value to match / 3 Key within same block / 4 Content string
function block_search_array
{
    search_array=()
 
    block_search_key=${1}
    block_search_value=${2}
    block_search_result_key=${3}
    block_search_content=${4}
 
    var_block=$(block_search "$block_search_key" "$block_search_value" "$block_search_result_key" "$block_search_content")
 
    while [ -n "$var_block" ]; do
        search_array[index++]="$var_block"
        block_search_content="${block_search_content/$block_search_value/}"
        var_block=$(block_search "$block_search_key" "$block_search_value" "$block_search_result_key" "$block_search_content")
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
DB_NAME=$(get_config DB_NAME "WordPressScalerDB")
DB_PASSWORD=$(get_config DB_PASSWORD)
DB_USERNAME=$(get_config DB_USERNAME "WordPressScalerDBUser")
DB_ENGINE=$(get_config DB_ENGINE "mariadb")

# Other settings
LOG_FILE="manage_wordpress.log"

# Create a new WordPress in your AWS account via AWS CLI.
if [[ $1 == "create" ]]; then
    rm $LOG_FILE
    echo "----- CREATE NEW WORDPRESS -----" >> "$LOG_FILE"
    
    echo "[1/5] Check VPC..."
    OUTPUT=$(aws ec2 describe-vpcs)
    echo "$OUTPUT" >> "$LOG_FILE"
    # "VpcId": "vpc-fffbe19a",
    # "IsDefault": true
 
    VPC_ID=$(block_search "IsDefault" "true" "VpcId" "$OUTPUT")
 
    if [[ $VPC_ID == "" ]]; then
        echo "Creating VPC..."
        # sample OUTPUT: { "Vpc": { "VpcId": "vpc-xxxxxxxx", "InstanceTenancy": "default", "State": "pending", "DhcpOptionsId": "dopt-xxxxxxxx", "CidrBlock": "172.31.0.0/16", "IsDefault": false } }    
        OUTPUT=$(aws ec2 create-vpc --cidr-block $VPC_IP_BLOCK)
        echo "$OUTPUT" >> "$LOG_FILE"
        VPC_ID=$(parse_json VpcId "$OUTPUT")
    fi    
    echo "   VPC ID: $VPC_ID"

    echo "[2/5] Check Security Group..."
    # TODO check for existing Security Group and re-use it!

    echo "Creating Security Group..."
    # sample OUTPUT: { "GroupId": "sg-xxxxxxxxx" }       
    OUTPUT=$(aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description "$SEC_GROUP_DESC" --vpc-id $VPC_ID)
    echo "$OUTPUT" >> "$LOG_FILE"
    SEC_GROUP_ID=$(parse_json GroupId "$OUTPUT")    
    echo "   Security Group Id: $SEC_GROUP_ID"
       
    echo "[3/5] Check RDS Database..."
    # TODO check for existing RDS instances and re-use it!
    
    # TODO if RDS does not exist, create it
    echo "Creating RDS Database..."
    OUTPUT=$(aws rds create-db-instance --engine $DB_ENGINE --db-instance-class $DB_INSTANCE_TYPE --db-instance-identifier $DB_NAME --master-user-password $DB_PASSWORD --master-username $DB_USERNAME --allocated-storage 5)
    echo "$OUTPUT" >> "$LOG_FILE"

    echo "[4/5] Check S3 Bucket..."
    # TODO check for existing S3 buckets and re-use it!
    
    # TODO if S3 bucket does not exist, create it
    echo "Creating S3 Bucket..."
            
    # TODO Check and create ELB                     
    # TODO Check and create AutoScalingGroups 
    # TODO Check and create CloudFront         
    # TODO Check and create Alarms                                 
               
    echo "[5/5] Creating EC2 instances..."        
    OUTPUT=$(aws ec2 run-instances --image-id $AMI --instance-type $INSTANCE_TYPE --count 2 --security-group-ids $SEC_GROUP_ID --region $REGION --block-device-mappings "[{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":false}}]" --cli-input-json file://ec2-config-simple.json)
    # --count 2
    # --subnet-id subnet-xxxxxxxx
    # --block-device-mappings "[{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":false}}]"
    # "InstanceId": "i-xxxxxxxx"
    echo "$OUTPUT" >> "$LOG_FILE"

    block_search_array "ImageId" "$AMI" "InstanceId" "$OUTPUT"
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "Adding tag $TAG to new EC2 Instance Id: $INSTANCE_ID"
	
	    # add tags to instance
        OUTPUT2=$(aws ec2 create-tags --resources $INSTANCE_ID --tags Key=Name,Value=$TAG)
    done 

    INSTANCE_NAME=$(echo $OUTPUT | sed 's/RESERVATION.*INSTANCE //' | sed 's/ .*//')

    times=0
    echo
    while [ 5 -gt $times ] && ! aws ec2 describe-instances $INSTANCE_NAME | grep -q "running"
    do
        times=$(( $times + 1 ))
        echo Attempt $times at verifying $INSTANCE_NAME is running...
    done

    echo

    if [ 5 -eq $times ]; then
        echo Instance $INSTANCE_NAME is not running. Exiting...
        exit    
    fi

    echo "[4/5] Creating S3 container..."
    OUTPUT3=$(aws s3api create-bucket --bucket wordpress-scaler --region $REGION --create-bucket-configuration LocationConstraint=$REGION --output text)
    aws s3api put-bucket-tagging --bucket wordpress-scaler --tagging TagSet="[{Key=Name,Value=$TAG}]"
    echo "$OUTPUT3" >> "$LOG_FILE"
    echo "$OUTPUT3"

    echo 
    echo "WordPress instances up and running."   
    echo 

# Delete the WordPress incl. database etc BE CAREFUL - this deletes all that the script created before.
elif [[ $1 == "delete" ]]; then
    echo -e "Please enter \"OK\" to delete all existing WordPress instances: \c "
    read  ok
    if [ $ok = "OK" ]; then

        echo "[1/2] Searching EC2 instances..."

        OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
        echo "$OUTPUT" >> "$LOG_FILE"
 
        block_search_array "ImageId" "$AMI" "InstanceId" "$OUTPUT"
        for INSTANCE_ID in "${search_array[@]}"
        do
	        echo "Deleting EC2 Instance $INSTANCE_ID..."
	        aws ec2 terminate-instances --instance-ids $INSTANCE_ID
	    done 


        echo "[2/2] Searching Security Group..."
        OUTPUT=$(aws ec2 describe-security-groups)
        echo "$OUTPUT" >> "$LOG_FILE"

        block_search_array "GroupName" "$SEC_GROUP_NAME" "GroupId" "$OUTPUT"
        for SEC_GROUP_ID in "${search_array[@]}"
        do
	        echo "Security Group: $SEC_GROUP_ID"
	
	        # TODO can only be one security group        
        done 

        echo "[3/5] Deleting Security Group $SEC_GROUP_ID..."
        OUTPUT=$(aws ec2 delete-security-group --group-id $SEC_GROUP_ID)
        echo "$OUTPUT" >> "$LOG_FILE"

        echo 
        echo "WordPress and all depending data deleted."           
        echo
    fi

# List settings and WordPress instances.
elif [[ $1 == "list" ]]; then

    echo "##################################"
    echo "# Plesk WordPress Scaler for AWS #"
    echo "##################################"
    echo 
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
    echo "RDS Database"
    echo "   DB Engine:                  $DB_ENGINE "
    echo "   DB Instance Type:           $DB_INSTANCE_TYPE "
    echo "   DB Name:                    $DB_NAME "
    echo "   DB User:                    $DB_USERNAME "
    echo "   DB Password:                $DB_PASSWORD "
    echo

    echo "Searching Security Groups..."
    OUTPUT=$(aws ec2 describe-security-groups)
    echo "$OUTPUT" >> "$LOG_FILE"

    # "GroupName": "WordPressScalerSecurityGroup",
    # "GroupId": "sg-xxxxxxxx"    
    # "VpcId": "vpc-xxxxxxxx"
    # or returns "InvalidGroup.NotFound" if no security group found

    block_search_array "GroupName" "$SEC_GROUP_NAME" "VpcId" "$OUTPUT"
    for VPC_ID in "${search_array[@]}"
    do
	    echo "   VPC ID: $VPC_ID"
    done   
    
    block_search_array "GroupName" "$SEC_GROUP_NAME" "GroupId" "$OUTPUT"
    for SEC_GROUP_ID in "${search_array[@]}"
    do
	    echo "   Security Group: $SEC_GROUP_ID"
    done   
    
    echo
    echo "Searching RDS instances..."
    
    # TODO search and list DB instances
    
    echo
    echo "Searching EC2 instances..."

    OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
    echo "$OUTPUT" >> "$LOG_FILE"
 
    block_search_array "ImageId" "$AMI" "InstanceId" "$OUTPUT"
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "   EC2 Instance $INSTANCE_ID"
    done    
    
# Display introduction and how to contribute.    
elif [[ $1 == "about" ]]; then

    echo "##################################"
    echo "# Plesk WordPress Scaler for AWS #"
    echo "##################################"
    echo 
    echo "Plesk WordPress Scaler automates provisioning of a highly available and auto-scaling WordPress to AWS."
    echo
    echo "It's Open Source! Contribute at https://github.com/plesk/wordpress-aws-scaler"
    echo "Follow us at https://twitter.com/PleskOfficial"
    echo

# Help page        
else 
    echo "##################################"
    echo "# Plesk WordPress Scaler for AWS #"
    echo "##################################"
    echo 
    echo "Plesk WordPress Scaler automates provisioning of a highly available and auto-scaling WordPress to AWS."
    echo
    echo "Commands:"
    echo "   manage_wordpress.sh create        Create a new WordPress in your AWS account via AWS CLI."
    echo "   manage_wordpress.sh delete        Delete the WordPress incl. database etc BE CAREFUL - this deletes all that the script created before."
    echo "   manage_wordpress.sh list          List settings and WordPress instances."
    echo "   manage_wordpress.sh about         Display introduction and how to contribute."
    echo 
    echo "It's Open Source! Contribute at https://github.com/plesk/wordpress-aws-scaler"
    echo "Follow us at https://twitter.com/PleskOfficial"
    echo
fi
