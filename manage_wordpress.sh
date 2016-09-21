#!/bin/bash

# TODOS
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

# Function to get a value from JSON text for a given key
# Parameters: 1 Content String / 2 Key to find
function get_value
{
    regex="\"${2}\":[[:space:]]?\"?([^,}\"]*)\"?(,|[[:space:]]\})"
 
    if [[ $1 =~ $regex ]]; then
        echo ${BASH_REMATCH[1]}
    else
        echo ''
    fi
}

# Function to get a specific value within a specified block, by using a key / pair search
# Parameters: 1 Content string / 2 Key within same block / 3 Key to find / 4 Value to match
function search_value
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
function search_values
{
    search_array=()
 
    content=${1}
    result_key=${2}
    key=${3}
    value=${4}

    var_block=$(search_value "$content" "$result_key" "$key" "$value")
 
    while [ -n "$var_block" ]; do
        search_array[index++]="$var_block"
        content="${content/$value/}"
        var_block=$(search_value "$content" "$result_key" "$key" "$value")
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

# Function to run and log a command
# Parameters: 1 command string to execute & log to file
function run_cmd
{
    CMD=$1
    echo "$CMD" >> "$LOG_FILE"
    OUTPUT=$($CMD)
    echo "$OUTPUT" >> "$LOG_FILE"
    echo $OUTPUT
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

NEWRELIC_KEY=$(get_config "$TAG" NEWRELIC_KEY "")
NEWRELIC_NAME=$(get_config "$TAG" NEWRELIC_NAME "$TAG")
WORDPRESS_TITLE=$(get_config "$TAG" WORDPRESS_TITLE "WordPress scaled on AWS")
WORDPRESS_DB_PREFIX=$(get_config "$TAG" WORDPRESS_DB_PREFIX "wp_")
WORDPRESS_USER_NAME=$(get_config "$TAG" WORDPRESS_USER_NAME "wordpress")
WORDPRESS_USER_PASSWORD=$(get_config "$TAG" WORDPRESS_USER_PASSWORD "xWH44tVfAoAqJx")
WORDPRESS_USER_EMAIL=$(get_config "$TAG" WORDPRESS_USER_EMAIL "jan@plesk.com")

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
    STEPS=10
    
   	# ----- CREATE VPC -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check VPC..."
    OUTPUT=$(aws ec2 describe-vpcs)
 
    VPC_ID=$(search_value "$OUTPUT" "VpcId" "IsDefault" "true")
    if [[ -z $VPC_ID ]]; then
        echo "      Creating VPC..."
        OUTPUT=$(run_cmd "aws ec2 create-vpc --cidr-block $VPC_IP_BLOCK")
        VPC_ID=$(get_value "$OUTPUT" "VpcId")
    fi    
    echo "      VPC ID: $VPC_ID"

   	# ----- CREATE SECURITY GROUP -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Security Group..."
    OUTPUT=$(aws ec2 describe-security-groups)

    SEC_GROUP_ID=$(search_value "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -z $SEC_GROUP_ID ]]; then
        echo "      Creating Security Group..."
        CMD="aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description \"$SEC_GROUP_DESC\" --vpc-id $VPC_ID"
    	echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description "$SEC_GROUP_DESC" --vpc-id $VPC_ID)
    	echo "$OUTPUT" >> "$LOG_FILE"
        SEC_GROUP_ID=$(get_value "$OUTPUT" "GroupId")   
        
        echo "      Adding Firewall Rules..."
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 22 --cidr 0.0.0.0/0        
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 80 --cidr 0.0.0.0/0        
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 443 --cidr 0.0.0.0/0        
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 3306 --cidr 0.0.0.0/0                
    fi
    echo "      Security Group Id: $SEC_GROUP_ID"        

   	# ----- CREATE S3 -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check S3 Bucket..."
    OUTPUT=$(aws s3api list-buckets)

    S3_BUCKET=$(search_value "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
    if [[ -z $S3_BUCKET ]]; then
        echo "      Creating S3 Bucket..."
        OUTPUT=$(run_cmd "aws s3api create-bucket --bucket $S3_BUCKET_NAME --region $REGION --create-bucket-configuration LocationConstraint=$REGION")
        S3_BUCKET=$(get_value "$OUTPUT" "Location")    
        if [[ -n $S3_BUCKET ]]; then
        	aws s3api put-bucket-tagging --bucket $S3_BUCKET_NAME --tagging TagSet="[{Key=Name,Value=$TAG}]"
        fi
    fi
    S3_URL=$(get_s3_url $S3_BUCKET_NAME)
    echo "      S3 Storage: $S3_URL"        

    # TODO Check and create CloudFront  
           
   	# ----- CREATE CLOUD FRONT -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Cloud Front..."
    # enable AWC CLI preview mode for CloudFront Support
    aws configure set preview.cloudfront true
    OUTPUT=$(aws cloudfront list-distributions)
    CF=$(get_value "$OUTPUT" "DomainName") 
    if [[ -z $CF ]]; then
        echo "      Creating Cloud Front..."
        echo "      Not implemented yet!"
        # TODO OUTPUT=$(run_cmd "aws cloudfront create-distribution --origin-domain-name $S3_BUCKET_NAME.s3.amazonaws.com")
    fi
    echo "      Cloud Front: $CF"        
	               
   	# ----- CREATE RDS -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check RDS Database..."

    OUTPUT=$(aws rds describe-db-instances)
    DB=$(search_value "$OUTPUT" "DBInstanceIdentifier" "DBInstanceIdentifier" "$DB_NAME")
    if [[ -z $DB ]]; then
        echo "      Creating RDS Database..."
        CMD="aws rds create-db-instance --engine $DB_ENGINE --db-instance-class $DB_INSTANCE_TYPE --db-instance-identifier $DB_NAME --db-name $DB_NAME --master-user-password $DB_PASSWORD --master-username $DB_USERNAME --allocated-storage 5 --vpc-security-group-ids $SEC_GROUP_ID --tags Key=Name,Value=$TAG"
    	echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws rds create-db-instance --engine $DB_ENGINE --db-instance-class $DB_INSTANCE_TYPE --db-instance-identifier $DB_NAME --db-name $DB_NAME --master-user-password $DB_PASSWORD --master-username $DB_USERNAME --allocated-storage 5 --vpc-security-group-ids $SEC_GROUP_ID --tags "Key=Name,Value=$TAG")
        # --db-security-groups <value>
        # --vpc-security-group-ids <value>
        # --license-model general-public-license
        # --publicly-accessible | --no-publicly-accessible
        # --storage-type <value>
    	echo "$OUTPUT" >> "$LOG_FILE"
	    DB=$(get_value "$OUTPUT" "DBInstanceIdentifier")
	fi

	OUTPUT=$(aws rds describe-db-instances --db-instance-identifier $DB_NAME)
    DB=$(search_value "$OUTPUT" "Address" "Port" "3306")	 
    if [[ -z $(echo $OUTPUT | grep "Address") ]]; then	  
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
	    DB=$(search_value "$OUTPUT" "Address" "Port" "3306")
	
	    if [[ -z $DB ]]; then
	        echo "      Database $DB_NAME is not running. Please wait a minute and re-run create!"
	        exit    
	    fi
	fi
	        
    echo "      RDS Database: $DB"        

   	# ----- CREATE ELB -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Elastic Loadbalancer..."
    OUTPUT=$(aws elb describe-load-balancers)

    ELB=$(search_value "$OUTPUT" "DNSName" "LoadBalancerName" "$ELB_NAME")
    if [[ -z $ELB ]]; then    
    	echo "      Creating ELB"
   	 	CMD="aws elb create-load-balancer --load-balancer-name $ELB_NAME --listeners \"Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80\" --security-groups $SEC_GROUP_ID --availability-zones eu-west-1a eu-west-1b eu-west-1c"
      	echo "$CMD" >> "$LOG_FILE"
   	 	OUTPUT=$(aws elb create-load-balancer --load-balancer-name $ELB_NAME --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" --security-groups $SEC_GROUP_ID --availability-zones eu-west-1a eu-west-1b eu-west-1c)
    	echo "$OUTPUT" >> "$LOG_FILE"
        ELB=$(get_value "$OUTPUT" "DNSName")    
    	aws elb add-tags --load-balancer-name $ELB_NAME --tags "Key=Name,Value=$TAG"
    fi
    echo "      Elastic Loadbalancer: $ELB"   
               
   	# ----- CREATE EC2 USER DATA SCRIPT -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Generating EC2 User Data script..."                       

cat >ec2-user-data.sh <<EOL
#!/bin/bash
docker pull janloeffler/wordpress-aws-scaler:latest
docker run -d -p 80:80 -p 443:443 -e WORDPRESS_DB_HOST='${DB}' -e WORDPRESS_DB_USER='${DB_USERNAME}' -e WORDPRESS_DB_PASSWORD='${DB_PASSWORD}' -e WORDPRESS_DB_NAME='${DB_NAME}' -e WORDPRESS_DB_PREFIX='${WORDPRESS_DB_PREFIX}' -e WORDPRESS_URL='http://${ELB}' -e WORDPRESS_TITLE='${WORDPRESS_TITLE}' -e WORDPRESS_USER_EMAIL='${WORDPRESS_USER_EMAIL}' -e NEWRELIC_KEY='${NEWRELIC_KEY}' -e NEWRELIC_NAME='${NEWRELIC_NAME}' -e S3_BUCKET='${S3_URL}' -it janloeffler/wordpress-aws-scaler:latest
EOL

    cat ec2-user-data.sh >> "$LOG_FILE"         

    # TODO check whether we can write docker parameters to a file and load it from there instead of a very long command

    # -e S3_SECRET='${S3_SECRET}'
    # -e S3_KEY='${S3_KEY}'
    # -e WORDPRESS_USER_NAME='${WORDPRESS_USER_NAMEXXX}'
    # -e WORDPRESS_USER_PASSWORD='${WORDPRESS_USER_PASSWORD}'

	# parameters used by Docker script
	# S3_KEY
	# S3_SECRET
	# S3_BUCKET
	# NEWRELIC_KEY
	# NEWRELIC_NAME
	# WORDPRESS_TITLE
	# WORDPRESS_URL
	# WORDPRESS_DB_PREFIX
	# WORDPRESS_USER_NAME
	# WORDPRESS_USER_PASSWORD
	# WORDPRESS_USER_EMAIL

   	# ----- CREATE LAUNCH CONFIGURATIONS -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Launch Configuration..."
    OUTPUT=$(aws autoscaling describe-launch-configurations)

    LC=$(search_value "$OUTPUT" "LaunchConfigurationName" "LaunchConfigurationName" "$LC_NAME")
    if [[ -z $LC ]]; then
        echo "      Getting first Key Pair"
        OUTPUT=$(aws ec2 describe-key-pairs)
        KEYNAME=$(get_value "$OUTPUT" "KeyName" "IsDefault" "none")

    	echo "      Creating Launch Configuration"
   	 	CMD="aws autoscaling create-launch-configuration --launch-configuration-name $LC_NAME --image-id $AMI --instance-type $INSTANCE_TYPE --key-name $KEYNAME --security-groups $SEC_GROUP_ID --block-device-mappings [{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}] --ebs-optimized --user-data file://ec2-user-data.sh"
      	echo "$CMD" >> "$LOG_FILE"
   	 	#OUTPUT=$(aws autoscaling create-launch-configuration --launch-configuration-name $LC_NAME --image-id $AMI --instance-type $INSTANCE_TYPE --security-groups $SEC_GROUP_ID --block-device-mappings "[{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}]" --ebs-optimized --user-data file://ec2-user-data.sh)
   	 	OUTPUT=$(aws autoscaling create-launch-configuration --launch-configuration-name $LC_NAME --image-id $AMI --instance-type $INSTANCE_TYPE --key-name $KEYNAME --security-groups $SEC_GROUP_ID --user-data file://ec2-user-data.sh)
    	echo "$OUTPUT" >> "$LOG_FILE"
        LC=$(get_value "$OUTPUT" "LaunchConfigurationName")    
    fi
    echo "      Launch Configuration: $LC" 

	# TODO Create CloudWatch Alarms

   	# ----- CREATE CLOUD WATCH ALARMS -----
	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check CloudWatch Alarms..."
	OUTPUT=$(aws cloudwatch describe-alarms --alarm-name-prefix $TAG)
	
    ALARM_NAME=$(search_value "$OUTPUT" "AlarmName" "MetricName" "CPUUtilization")
    if [[ -z $ALARM_NAME ]]; then    
    	echo "      Creating CloudWatch Alarms"
		echo "      CloudWatch Alarm: $ALARM_NAME -> Not implemented yet!"
		# TODO finish CloudWatch - might required SNS queue?
        # aws cloudwatch put-metric-alarm --alarm-name ${TAG}_cpu_high --alarm-description "$TAG Alarm when CPU exceeds 70 percent" --metric-name CPUUtilization --namespace AWS/EC2 --statistic Average --period 300 --threshold 70 --comparison-operator GreaterThanThreshold  --dimensions "Name=InstanceId,Value=i-12345678" --evaluation-periods 2 --alarm-actions arn:aws:sns:${REGION}:111122223333:MyTopic --unit Percent
	else
		OUTPUT=$(aws cloudwatch describe-alarms --alarm-name-prefix $TAG)
		
	    search_values "$OUTPUT" "AlarmName" "MetricName" "CPUUtilization"
	    for ALARM_NAME in "${search_array[@]}"
	    do
		    echo "      CloudWatch Alarm: $ALARM_NAME"
	    done    	
    fi    

   	# ----- CREATE AUTO SCALING GROUPS -----
    STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Auto Scaling Group..."
    OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)

    ASG=$(search_value "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    if [[ -z $ASG ]]; then    
    	echo "      Creating Auto Scaling Group"
   	 	CMD="aws autoscaling create-auto-scaling-group --auto-scaling-group-name $ASG_NAME --launch-configuration-name $LC_NAME --min-size $EC2_MIN_INSTANCES --max-size $EC2_MAX_INSTANCES --load-balancer-names $ELB_NAME --availability-zones eu-west-1a eu-west-1b eu-west-1c --health-check-type ELB --health-check-grace-period 60 --tags Key=Name,Value=$TAG"
      	echo "$CMD" >> "$LOG_FILE"
   	 	OUTPUT=$(aws autoscaling create-auto-scaling-group --auto-scaling-group-name $ASG_NAME --launch-configuration-name $LC_NAME --min-size $EC2_MIN_INSTANCES --max-size $EC2_MAX_INSTANCES --load-balancer-names $ELB_NAME --availability-zones eu-west-1a eu-west-1b eu-west-1c --health-check-type ELB --health-check-grace-period 60 --tags "Key=Name,Value=$TAG")
    	# [--desired-capacity <value>]
    	# [--default-cooldown <value>]
    	# [--target-group-arns <value>]
    	# [--placement-group <value>]
    	# [--vpc-zone-identifier <value>]
    	# [--termination-policies <value>]
    	# [--new-instances-protected-from-scale-in | --no-new-instances-protected-from-scale-in]
    	echo "$OUTPUT" >> "$LOG_FILE"
        ASG=$(get_value "$OUTPUT" "AutoScalingGroupARN")    
    fi
    echo "      Auto Scaling Group: $ASG" 

	aws autoscaling resume-processes --auto-scaling-group-name $ASG_NAME
               
    # ----- CREATE EC2 INSTANCES -----
   	# STEP=$((STEP+1))
    # echo "[$STEP/$STEPS] Creating EC2 instances..."        
    # CMD="aws ec2 run-instances --image-id $AMI --instance-type $INSTANCE_TYPE --count $EC2_MIN_INSTANCES --security-group-ids $SEC_GROUP_ID --region $REGION --block-device-mappings [{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}] --ebs-optimized --user-data file://ec2-user-data.sh"
  	# echo "$CMD" >> "$LOG_FILE"
    # OUTPUT=$(aws ec2 run-instances --image-id $AMI --instance-type $INSTANCE_TYPE --count $EC2_MIN_INSTANCES --security-group-ids $SEC_GROUP_ID --region $REGION --block-device-mappings "[{\"VirtualName\":\"$DEVICE_NAME\",\"DeviceName\":\"/dev/sdb\",\"Ebs\":{\"VolumeSize\":10,\"DeleteOnTermination\":true}}]" --ebs-optimized --user-data file://ec2-user-data.sh)
    # --subnet-id subnet-xxxxxxxx
    # "InstanceId": "i-xxxxxxxx"
    # echo "$OUTPUT" >> "$LOG_FILE"
	# 
    # search_values "$OUTPUT" "InstanceId" "ImageId" "$AMI"
    # for INSTANCE_ID in "${search_array[@]}"
    # do
	#     echo "      Adding tag $TAG to new EC2 Instance Id: $INSTANCE_ID"
    #     OUTPUT2=$(aws ec2 create-tags --resources $INSTANCE_ID --tags Key=Name,Value=$TAG)
    # done 

    OUTPUT=$(aws autoscaling describe-auto-scaling-instances)
 
    i=0
    search_values "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
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

    if [ 5 -eq $times ]; then
        echo EC2 Instance $INSTANCE_ID is not running. Exiting...
        exit    
    fi

    OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
 
    i=0
    search_values "$OUTPUT" "InstanceId" "ImageId" "$AMI"
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

        # ----- STOP AUTO SCALING -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Auto Scaling Group..."
    	OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)

    	ASG=$(search_value "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    	if [[ -n $ASG ]]; then    
		    echo "      Setting max instances to 0 for Auto Scaling Group \"$ASG\"..."     
            OUTPUT=$(run_cmd "aws autoscaling update-auto-scaling-group --auto-scaling-group-name $ASG_NAME --max-size 0 --min-size 0")
		else
            echo "      No Auto Scaling Group found"        
		fi

        # ----- DELETE INSTANCES -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Auto Scaling Instances..."
    	OUTPUT=$(aws autoscaling describe-auto-scaling-instances)

    	i=0
    	search_values "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    	for INSTANCE_ID in "${search_array[@]}"
    	do
	    	# echo "      Detaching Auto Scaling Instance \"$INSTANCE_ID\"..."
	        # OUTPUT=$(run_cmd "aws autoscaling detach-instances --instance-ids $INSTANCE_ID --auto-scaling-group-name $ASG_NAME --should-decrement-desired-capacity")

	    	echo "      Deleting Auto Scaling Instance \"$INSTANCE_ID\"..."
	        OUTPUT=$(run_cmd "aws ec2 terminate-instances --instance-ids $INSTANCE_ID")
	        #OUTPUT=$(run_cmd "aws autoscaling terminate-instance-in-auto-scaling-group --instance-id $INSTANCE_ID --should-decrement-desired-capacity")
	   		i=$((i+1))
    	done    
 		echo "      $i Auto Scaling Instances deleted"  

        # ----- DELETE LAUNCH CONFIGURATIONS -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Launch Configurations..."
    	OUTPUT=$(aws autoscaling describe-launch-configurations)

	    LC=$(search_value "$OUTPUT" "LaunchConfigurationName" "LaunchConfigurationName" "$LC_NAME")
    	if [[ -n $LC ]]; then    
		    echo "      Deleting Launch Configuration \"$LC\"..."     
            OUTPUT=$(run_cmd "aws autoscaling delete-launch-configuration --launch-configuration-name $LC_NAME")
		else
            echo "      No Launch Configurations found"        
		fi

        # ----- DELETE CLOUD WATCH -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching CloudWatch Alarms..."
		OUTPUT=$(aws cloudwatch describe-alarms --alarm-name-prefix $TAG)
	
    	i=0
    	search_values "$OUTPUT" "AlarmName" "MetricName" "CPUUtilization"
    	for ALARM_NAME in "${search_array[@]}"
    	do
	  		echo "      Deleting CloudWatch Alarm \"$ALARM_NAME\"..."
			OUTPUT=$(run_cmd "aws cloudwatch delete-alarms --alarm-name $ALARM_NAME")
	    	i=$((i+1))
    	done    
    
		echo "      $i CloudWatch Alarms deleted"

        # ----- DELETE AUTO SCALING GROUP -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Auto Scaling Group..."
    	OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)
    	ASG=$(search_value "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    	if [[ -n $ASG ]]; then    
		    echo "      Deleting Auto Scaling Group \"$ASG_NAME\"..."     
            OUTPUT=$(run_cmd "aws autoscaling delete-auto-scaling-group --auto-scaling-group-name $ASG_NAME --force-delete")
		else
            echo "      No Auto Scaling Group found"        
		fi

        # ----- DELETE EC2 INSTANCES -----
	   	# STEP=$((STEP+1))
        # echo "[$STEP/$STEPS] Searching remaining EC2 instances..."
        # OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
 
 		# i=0
        # search_values "$OUTPUT" "InstanceId" "ImageId" "$AMI"
        # for INSTANCE_ID in "${search_array[@]}"
        # do
        # 	# check if instance is already terminated
        # 	#"Name": "running"
	    #     #STATE=$(search_value "$OUTPUT" "Name" "InstanceId" "$INSTANCE_ID")
        # 	
	    #    echo "      Deleting EC2 Instance \"$INSTANCE_ID\"..."
	    #    OUTPUT=$(run_cmd "aws ec2 terminate-instances --instance-ids $INSTANCE_ID")
        #    i=$((i+1))
	    # done 
 		# echo "      $i EC2 Instances deleted"  

        # ----- DELETE ELB -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Elastic Loadbalancer..."
    	OUTPUT=$(aws elb describe-load-balancers)

    	ELB=$(search_value "$OUTPUT" "LoadBalancerName" "LoadBalancerName" "$ELB_NAME")
    	if [[ -n $ELB ]]; then    
		    echo "      Deleting Elastic Loadbalancer \"$ELB\"..."     
            OUTPUT=$(run_cmd "aws elb delete-load-balancer --load-balancer-name $ELB_NAME")
		else
            echo "      No Elastic Loadbalancer found"        
		fi

        # ----- DELETE RDS -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching RDS Database..."
        OUTPUT=$(aws rds describe-db-instances)
        DB=$(search_value "$OUTPUT" "DBInstanceIdentifier" "DBInstanceIdentifier" "$DB_NAME")
        if [[ -n $DB ]]; then
            echo "      Deleting RDS Database \"$DB\"..."
            OUTPUT=$(run_cmd "aws rds delete-db-instance --db-instance-identifier $DB_NAME --skip-final-snapshot")
        else
            echo "      No RDS Database found"        
        fi

        # ----- DELETE S3 -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching S3 Bucket..."
        OUTPUT=$(aws s3api list-buckets)

        S3_BUCKET=$(search_value "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
        if [[ -n $S3_BUCKET ]]; then
		    S3_URL=$(get_s3_url $S3_BUCKET_NAME)
            echo "      Deleting S3 Bucket \"$S3_URL\"..."
            OUTPUT=$(run_cmd "aws s3api delete-bucket --bucket $S3_BUCKET_NAME")
        else
            echo "      No S3 Bucket found"        
        fi

        # ----- DELETE CLOUD FRONT -----
		# TODO Delete CloudFront
        # enable AWC CLI preview mode for CloudFront Support
        aws configure set preview.cloudfront true
        OUTPUT=$(aws cloudfront list-distributions)
        CF=$(get_value "$OUTPUT" "DomainName") 
        if [[ -n $CF ]]; then
            echo "      Deleting CLoud Front \"$CF\"..."
            echo "      Not implemented yet!"
            # TODO get-distribution-config
            # TODO aws cloudfront update-distribution --id $CF_ID
            # TODO aws cloudfront delete-distribution --id $CF_ID
        fi
        echo "      No Cloud Front found"        

        # ----- DELETE SECURITY GROUP -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Security Group..."
        OUTPUT=$(aws ec2 describe-security-groups)

        SEC_GROUP_ID=$(search_value "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
        if [[ -n $SEC_GROUP_ID ]]; then
            echo "      Deleting Security Group \"$SEC_GROUP_ID\"..."
            OUTPUT=$(run_cmd "aws ec2 delete-security-group --group-id $SEC_GROUP_ID")
        else
            echo "      No Security Group found"        
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
    echo "WordPress"
    echo "   WordPress Site Title:       $WORDPRESS_TITLE"
    echo "   WordPress DB Prefix:        $WORDPRESS_DB_PREFIX"
    echo "   WordPress Username:         $WORDPRESS_USER_NAME"
    echo "   WordPress Password:         $WORDPRESS_USER_PASSWORD"
    echo "   WordPress E-Mail:           $WORDPRESS_USER_EMAIL"
    echo
    echo "New Relic"
    echo "   New Relic Name:             $NEWRELIC_NAME"
    echo "   New Relic Key:              $NEWRELIC_KEY"
    echo
    echo "Resources"
    echo "------------------------------------------------------"

    # ----- LIST VPC -----
    OUTPUT=$(aws ec2 describe-security-groups)
    VPC_ID=$(search_value "$OUTPUT" "VpcId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -z $VPC_ID ]]; then
        VPC_ID="none"
    fi
	echo "   VPC ID:                     $VPC_ID"

    # ----- LIST SECURITY GROUP -----
    SEC_GROUP_ID=$(search_value "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -z $SEC_GROUP_ID ]]; then
        SEC_GROUP_ID="none"
    fi
	echo "   Security Group:             $SEC_GROUP_ID"

    # ----- LIST CLOUD FRONT -----
    # enable AWC CLI preview mode for CloudFront Support
    aws configure set preview.cloudfront true
    OUTPUT=$(aws cloudfront list-distributions)
    CF=$(get_value "$OUTPUT" "DomainName") 
    if [[ -z $CF ]]; then
        CF="none"
    fi
    echo "   CloudFront:                 $CF"   

    # ----- LIST S3 -----
    OUTPUT=$(aws s3api list-buckets)
    S3_BUCKET=$(search_value "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
    if [[ -z $S3_BUCKET ]]; then
        S3_URL="none"
    else
    	S3_URL=$(get_s3_url $S3_BUCKET_NAME)
    fi 
	echo "   S3 Storage:                 $S3_URL"
     
    # ----- LIST RDS -----
    OUTPUT=$(aws rds describe-db-instances)
    DB=$(search_value "$OUTPUT" "DBInstanceIdentifier" "DBInstanceIdentifier" "$DB_NAME")
    if [[ -z $DB ]]; then
	    DB="none"
	else
    	OUTPUT=$(aws rds describe-db-instances --db-instance-identifier $DB_NAME)
    	DB=$(search_value "$OUTPUT" "Address" "Port" "3306")
    fi
	echo "   RDS Database:               $DB"

    # ----- LIST ELB -----
    OUTPUT=$(aws elb describe-load-balancers)
    ELB=$(search_value "$OUTPUT" "DNSName" "LoadBalancerName" "$ELB_NAME")
    if [[ -z $ELB ]]; then    
        ELB="none"    
    fi
    echo "   Elastic Loadbalancer:       $ELB" 
 
    # ----- LIST LAUNCH CONFIGURATION -----
    OUTPUT=$(aws autoscaling describe-launch-configurations)
    LC=$(search_value "$OUTPUT" "LaunchConfigurationName" "LaunchConfigurationName" "$LC_NAME")
    if [[ -z $LC ]]; then    
    	LC="none"    
    fi
    echo "   Launch Configurations:      $LC" 
       
    # ----- LIST AUTO-SCALING-GROUP -----
    OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)
    ASG=$(search_value "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    if [[ -z $ASG ]]; then   
         ASG="none"
    fi
    echo "   Auto Scaling Group:         $ASG" 
    
    # ----- LIST CLOUD WATCH -----
	OUTPUT=$(aws cloudwatch describe-alarms --alarm-name-prefix $TAG)	
    i=0
    search_values "$OUTPUT" "AlarmName" "MetricName" "CPUUtilization"
    for ALARM_NAME in "${search_array[@]}"
    do
	    echo "   CloudWatch Alarm:           $ALARM_NAME"
	    i=$((i+1))
    done    
    
	echo "   CloudWatch Alarms:          $i" 
	
    # ----- LIST INSTANCES -----
    OUTPUT=$(aws autoscaling describe-auto-scaling-instances)
    i=0
    search_values "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "   Auto Scaling Instance:      $INSTANCE_ID"
	    i=$((i+1))
    done    
    
	echo "   Auto Scaling Instances:     $i" 
	    
    # ----- LIST EC2 INSTANCES -----
    # OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG")
    #
    # i=0
    # search_values "$OUTPUT" "InstanceId" "ImageId" "$AMI"
    # for INSTANCE_ID in "${search_array[@]}"
    # do
	#     echo "   EC2 Instance:               $INSTANCE_ID"
	#     i=$((i+1))
    # done    
    # 
	# echo "   EC2 Instances:              $i"  
	
	echo 
    
# ----------- CONSOLE -----------
# Print console output of first EC2 instance found.
elif [[ $1 == "console" ]]; then

    OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG") 
    INSTANCE_ID=$(search_value "$OUTPUT" "InstanceId" "ImageId" "$AMI")
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
