#!/bin/bash

# TODOS
# - Create CloudWatch Alarms

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
        regex="(\{[^\{]*\"${3}\":[[:space:]]?\"?[^\"]*${4}[^\"]*\"?[^}]*\})"
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

    result=$(search_value "$content" "$result_key" "$key" "$value")
 
    while [ -n "$result" ]; do
        search_array[index++]="$result"
        content="${content/$result/}"
        result=$(search_value "$content" "$result_key" "$key" "$value")
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
	suffix=".s3.amazonaws.com"
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
    TAG="pleskwp"
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
DB_INSTANCE_TYPE=$(get_config "$TAG" DB_INSTANCE_TYPE "db.m3.medium")
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

DOMAIN_NAME=$(get_config "$TAG" DOMAIN_NAME "")
NEWRELIC_KEY=$(get_config "$TAG" NEWRELIC_KEY "")
NEWRELIC_NAME=$(get_config "$TAG" NEWRELIC_NAME "$TAG")
WORDPRESS_TITLE=$(get_config "$TAG" WORDPRESS_TITLE "WordPress scaled on AWS")
WORDPRESS_DB_PREFIX=$(get_config "$TAG" WORDPRESS_DB_PREFIX "wp_")
WORDPRESS_USER_NAME=$(get_config "$TAG" WORDPRESS_USER_NAME "wordpress")
WORDPRESS_USER_PASSWORD=$(get_config "$TAG" WORDPRESS_USER_PASSWORD "xWH44tVfAoAqJx")
WORDPRESS_USER_EMAIL=$(get_config "$TAG" WORDPRESS_USER_EMAIL "jan@plesk.com")

IAM_USER=$(get_config "$TAG" IAM_USER "$TAG")
IAM_USER_CREDENTIALS="$TAG-credentials.log"

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
    STEPS=13

   	# ----- CREATE VPC -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check VPC..."
    OUTPUT=$(aws ec2 describe-vpcs)
    VPC_ID=$(search_value "$OUTPUT" "VpcId" "IsDefault" "true")
    if [[ -z $VPC_ID ]]; then
        echo "       Creating VPC..."
        OUTPUT=$(run_cmd "aws ec2 create-vpc --cidr-block $VPC_IP_BLOCK")
        VPC_ID=$(get_value "$OUTPUT" "VpcId")
    fi    
    echo "       VPC ID: $VPC_ID"

   	# ----- CREATE SECURITY GROUP -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Security Group..."
    OUTPUT=$(aws ec2 describe-security-groups)
    SEC_GROUP_ID=$(search_value "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
    if [[ -z $SEC_GROUP_ID ]]; then
        echo "       Creating Security Group..."
        CMD="aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description \"$SEC_GROUP_DESC\" --vpc-id $VPC_ID"
    	echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description "$SEC_GROUP_DESC" --vpc-id $VPC_ID)
    	echo "$OUTPUT" >> "$LOG_FILE"
        SEC_GROUP_ID=$(get_value "$OUTPUT" "GroupId")   
        
        echo "       Adding Firewall Rules..."
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 22 --cidr 0.0.0.0/0        
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 80 --cidr 0.0.0.0/0        
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 443 --cidr 0.0.0.0/0        
		aws ec2 authorize-security-group-ingress --group-id $SEC_GROUP_ID --protocol tcp --port 3306 --cidr 0.0.0.0/0                
    fi
    echo "       Security Group Id: $SEC_GROUP_ID"        

    # ----- CREATE IAM USER -----
    STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check IAM USER..."
    OUTPUT=$(aws iam list-users)
    HAS_USER=$(search_value "$OUTPUT" "UserName" "UserName" "$IAM_USER")
    if [[ -z $HAS_USER ]]; then
        echo "       Creating IAM User..."
        run_cmd "aws iam create-user --user-name $IAM_USER"
        run_cmd "aws iam attach-user-policy --user-name $IAM_USER --policy-arn arn:aws:iam::aws:policy/AmazonS3FullAccess"
    fi
    echo "       IAM USER: $IAM_USER"

    if [ -f $IAM_USER_CREDENTIALS ]; then
        echo "       Getting IAM User credentials..."
        IAM_USER_KEY=$(head -n 1 $IAM_USER_CREDENTIALS)
        IAM_USER_SECRET=$(sed '2q;d' $IAM_USER_CREDENTIALS)
    else
        echo "       Creating IAM User Credentials..."
        CREDENTIALS=$(run_cmd "aws iam create-access-key --user-name $IAM_USER")
        IAM_USER_KEY=$(get_value "$CREDENTIALS" "AccessKeyId")
        IAM_USER_SECRET=$(get_value "$CREDENTIALS" "SecretAccessKey")

        echo "$IAM_USER_KEY" >> "$IAM_USER_CREDENTIALS"
        echo "$IAM_USER_SECRET" >> "$IAM_USER_CREDENTIALS"
    fi

   	# ----- CREATE S3 -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check S3 Bucket..."
    OUTPUT=$(aws s3api list-buckets)
    S3_BUCKET=$(search_value "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
    if [[ -z $S3_BUCKET ]]; then
        echo "       Creating S3 Bucket..."
        OUTPUT=$(run_cmd "aws s3api create-bucket --bucket $S3_BUCKET_NAME --region $REGION --acl public-read --create-bucket-configuration LocationConstraint=$REGION")
        S3_BUCKET=$(get_value "$OUTPUT" "Location")    
        if [[ -n $S3_BUCKET ]]; then
        	aws s3api put-bucket-tagging --bucket $S3_BUCKET_NAME --tagging TagSet="[{Key=Name,Value=$TAG}]"
        fi
    fi
    S3_URL=$(get_s3_url $S3_BUCKET_NAME)
    echo "       S3 Storage: $S3_URL"

   	# ----- CREATE CLOUDFRONT -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check CloudFront..."
    # enable AWC CLI preview mode for CloudFront Support
    aws configure set preview.cloudfront true
    OUTPUT=$(aws cloudfront list-distributions)
    CF=$(get_value "$OUTPUT" "DomainName")
    if [[ -z $CF ]]; then
        echo "       Creating CloudFront..."
        OUTPUT=$(run_cmd "aws cloudfront create-distribution --origin-domain-name $S3_BUCKET_NAME.s3.amazonaws.com")
        CF=$(get_value "$OUTPUT" "DomainName")

        #overwrite S3_URL so that WordPress loads assets over CloudFront
        S3_URL="https://$CF"
    fi
    echo "       CloudFront: $CF"

   	# ----- CREATE RDS -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check RDS Database..."

    OUTPUT=$(aws rds describe-db-instances)
    DB=$(search_value "$OUTPUT" "DBInstanceIdentifier" "DBInstanceIdentifier" "$DB_NAME")
    if [[ -z $DB ]]; then
        echo "       Creating RDS Database..."
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
		echo "       Checking until Database is available and has a dns name. This can take a while..."
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
	        echo "       Database $DB_NAME is not running. Please wait a minute and re-run create!"
            echo
	        exit    
	    fi
	fi
	        
    echo "       RDS Database: $DB"        

   	# ----- CREATE ELB -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Elastic Loadbalancer..."
    OUTPUT=$(aws elb describe-load-balancers)
    ELB=$(search_value "$OUTPUT" "DNSName" "LoadBalancerName" "$ELB_NAME")
    ELB_ID=$(search_value "$OUTPUT" "CanonicalHostedZoneNameID")
    if [[ -z $ELB ]]; then
        OUTPUT=$(aws iam list-server-certificates)
        ARN=$(search_value "$OUTPUT" "Arn" "ServerCertificateName" "$TAG")

        if [[ -z $ARN ]]; then
            #Required
            commonname=$DOMAIN_NAME

            #Change to your company details
            country=EU
            state=$DOMAIN_NAME
            locality=$DOMAIN_NAME
            organization=$DOMAIN_NAME
            organizationalunit=IT
            email=no-reply@$DOMAIN_NAME

            #create the request
            openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout $DOMAIN_NAME.key -out $DOMAIN_NAME.crt \
                -subj "/C=$country/ST=$state/L=$locality/O=$organization/OU=$organizationalunit/CN=$commonname/emailAddress=$email"

            openssl rsa -in $DOMAIN_NAME.key -text > $DOMAIN_NAME-private.pem
            openssl x509 -inform PEM -in $DOMAIN_NAME.crt > $DOMAIN_NAME-public.pem

            OUTPUT=$(aws iam upload-server-certificate --server-certificate-name $TAG --certificate-body file://$DOMAIN_NAME-public.pem --private-key file://$DOMAIN_NAME-private.pem)
            ARN=$(search_value "$OUTPUT" "Arn")
            echo "$OUTPUT" >> "$LOG_FILE"
            sleep 2s
        fi

        echo "       Creating ELB"
        CMD="aws elb create-load-balancer --load-balancer-name $ELB_NAME --listeners \"Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80\" \"Protocol=https,LoadBalancerPort=443,InstanceProtocol=http,InstancePort=80,SSLCertificateId=$ARN\" --security-groups $SEC_GROUP_ID --availability-zones eu-west-1a eu-west-1b eu-west-1c"
        echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws elb create-load-balancer --load-balancer-name $ELB_NAME --listeners "Protocol=HTTP,LoadBalancerPort=80,InstanceProtocol=HTTP,InstancePort=80" "Protocol=https,LoadBalancerPort=443,InstanceProtocol=http,InstancePort=80,SSLCertificateId=$ARN" --security-groups $SEC_GROUP_ID --availability-zones eu-west-1a eu-west-1b eu-west-1c)
        echo "$OUTPUT" >> "$LOG_FILE"
        ELB=$(get_value "$OUTPUT" "DNSName")
        ELB_ID=$(search_value "$OUTPUT" "CanonicalHostedZoneNameID")
        aws elb add-tags --load-balancer-name $ELB_NAME --tags "Key=Name,Value=$TAG"
        aws elb configure-health-check --load-balancer-name $ELB_NAME --health-check Target=HTTP:80/readme.html,Interval=30,UnhealthyThreshold=10,HealthyThreshold=10,Timeout=5
    fi
    echo "       Elastic Loadbalancer: $ELB"

    STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Route53 host zone..."
    OUTPUT=$(aws route53 list-hosted-zones-by-name --dns-name $DOMAIN_NAME)
    ZONE_ID=$(search_value "$OUTPUT" "Id")
    if [[ -z $ZONE_ID ]]; then
        CALLER=$(date +"%Y-%m-%d-%H:%M")
        echo "       Creating host zone"
        CMD="aws route53 create-hosted-zone --name $DOMAIN_NAME --caller-reference $CALLER"
        echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws route53 create-hosted-zone --name $DOMAIN_NAME --caller-reference $CALLER)
        echo "$OUTPUT" >> "$LOG_FILE"
        ZONE_ID=$(get_value "$OUTPUT" "Id")   

        echo "{ \"Changes\": [ { \"Action\": \"UPSERT\", \"ResourceRecordSet\": { \"Name\": \"$DOMAIN_NAME\", \"Type\": \"A\", \"AliasTarget\": { \"HostedZoneId\": \"$ELB_ID\", \"DNSName\": \"$ELB\", \"EvaluateTargetHealth\": false } } }, { \"Action\": \"UPSERT\", \"ResourceRecordSet\": { \"Name\": \"www.$DOMAIN_NAME\", \"Type\": \"A\", \"AliasTarget\": { \"HostedZoneId\": \"$ELB_ID\", \"DNSName\": \"$ELB\", \"EvaluateTargetHealth\": false } } } ] }" > change-resource-record-sets.json
        aws route53 change-resource-record-sets --hosted-zone-id $ZONE_ID --change-batch file://change-resource-record-sets.json
        rm change-resource-record-sets.json

    fi
    echo "       Route53 Zone ID: $ZONE_ID"

    # ----- CREATE EC2 USER DATA SCRIPT -----
    STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Generating EC2 User Data script..."                       

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

cat >ec2-user-data.sh <<EOL
#!/bin/bash
docker pull janloeffler/wordpress-aws-scaler:latest
docker run -d -p 80:80 -p 443:443 -e WORDPRESS_DB_HOST='${DB}' -e WORDPRESS_DB_USER='${DB_USERNAME}' -e WORDPRESS_DB_PASSWORD='${DB_PASSWORD}' -e WORDPRESS_DB_NAME='${DB_NAME}' -e WORDPRESS_DB_PREFIX='${WORDPRESS_DB_PREFIX}' -e WORDPRESS_URL='https://${DOMAIN_NAME}' -e WORDPRESS_TITLE='${WORDPRESS_TITLE}' -e WORDPRESS_USER_EMAIL='${WORDPRESS_USER_EMAIL}' -e NEWRELIC_KEY='${NEWRELIC_KEY}' -e NEWRELIC_NAME='${NEWRELIC_NAME}' -e S3_KEY='${IAM_USER_KEY}' -e S3_SECRET='${IAM_USER_SECRET}' -e S3_BUCKET='${S3_BUCKET_NAME}' -e S3_BUCKET_URL='${S3_URL}' -it janloeffler/wordpress-aws-scaler:latest
EOL

    cat ec2-user-data.sh >> "$LOG_FILE"         

   	# ----- CREATE LAUNCH CONFIGURATIONS -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Launch Configuration..."
    OUTPUT=$(aws autoscaling describe-launch-configurations)
    LC=$(search_value "$OUTPUT" "LaunchConfigurationName" "LaunchConfigurationName" "$LC_NAME")
    if [[ -z $LC ]]; then
        echo "       Getting first Key Pair"
        OUTPUT=$(aws ec2 describe-key-pairs)
        KEYNAME=$(get_value "$OUTPUT" "KeyName" "IsDefault" "none")

    	echo "       Creating Launch Configuration"
   	 	OUTPUT=$(run_cmd "aws autoscaling create-launch-configuration --launch-configuration-name $LC_NAME --image-id $AMI --instance-type $INSTANCE_TYPE --key-name $KEYNAME --security-groups $SEC_GROUP_ID --user-data file://ec2-user-data.sh")
        LC=$(get_value "$OUTPUT" "LaunchConfigurationName")    
    fi
    echo "       Launch Configuration: $LC" 

   	# ----- CREATE SNS QUEUE -----
	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Simple Notification Service Topics (SNS)..."
    OUTPUT=$(aws sns list-topics)	
    SNS_ARN=$(search_value "$OUTPUT" "TopicArn" "TopicArn" "$TAG")
    if [[ -z $SNS_ARN ]]; then
    	echo "       Creating Notification Service Topics (SNS)..."
        OUTPUT=$(run_cmd "aws sns create-topic --name $TAG")
        SNS_ARN=$(get_value "$OUTPUT" "TopicArn")
    fi
    echo "       Notification Topic (SNS): $SNS_ARN"

   	# ----- CREATE CLOUD WATCH ALARMS -----
	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check CloudWatch Alarms..."
	OUTPUT=$(aws cloudwatch describe-alarms --alarm-name-prefix $TAG)
	
	# TODO finish CloudWatch - requires SNS queue
    ALARM_NAME=$(search_value "$OUTPUT" "AlarmName" "MetricName" "CPUUtilization")
    if [[ -z $ALARM_NAME ]]; then    
    	echo "       Creating CloudWatch Alarms for up- and down-scaling"
		
        echo "       CloudWatch Alarm (Up-Scaling): ${TAG}_cpu_high"
        CMD="aws cloudwatch put-metric-alarm --alarm-name ${TAG}_cpu_high --alarm-description \"$TAG Alarm when CPU exceeds 70 percent\" --metric-name CPUUtilization --namespace AWS/EC2 --statistic Average --period 300 --threshold 70 --comparison-operator GreaterThanThreshold --evaluation-periods 2 --alarm-actions ${SNS_ARN} --unit Percent"
      	echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws cloudwatch put-metric-alarm --alarm-name ${TAG}_cpu_high --alarm-description "$TAG Alarm when CPU exceeds 70 percent" --metric-name CPUUtilization --namespace AWS/EC2 --statistic Average --period 300 --threshold 70 --comparison-operator GreaterThanThreshold --evaluation-periods 2 --alarm-actions ${SNS_ARN} --unit Percent)
    	echo "$OUTPUT" >> "$LOG_FILE"

		echo "       CloudWatch Alarm (Down-Scaling): ${TAG}_cpu_low"
        CMD="aws cloudwatch put-metric-alarm --alarm-name ${TAG}_cpu_low --alarm-description \"$TAG Alarm when CPU is lower than 20 percent\" --metric-name CPUUtilization --namespace AWS/EC2 --statistic Average --period 300 --threshold 20 --comparison-operator LessThanThreshold --evaluation-periods 2 --alarm-actions ${SNS_ARN} --unit Percent"
      	echo "$CMD" >> "$LOG_FILE"
        OUTPUT=$(aws cloudwatch put-metric-alarm --alarm-name ${TAG}_cpu_low --alarm-description "$TAG Alarm when CPU is lower than 20 percent" --metric-name CPUUtilization --namespace AWS/EC2 --statistic Average --period 300 --threshold 20 --comparison-operator LessThanThreshold --evaluation-periods 2 --alarm-actions ${SNS_ARN} --unit Percent)
    	echo "$OUTPUT" >> "$LOG_FILE"
	else
		OUTPUT=$(aws cloudwatch describe-alarms --alarm-name-prefix $TAG)
	    search_values "$OUTPUT" "AlarmName" "MetricName" "CPUUtilization"
	    for ALARM_NAME in "${search_array[@]}"
	    do
		    echo "       CloudWatch Alarm: $ALARM_NAME"
	    done    	
    fi    

   	# ----- CREATE AUTO SCALING GROUPS -----
    STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Auto Scaling Group..."
    OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)
    ASG=$(search_value "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    if [[ -z $ASG ]]; then    
    	echo "       Creating Auto Scaling Group"
   	 	CMD="aws autoscaling create-auto-scaling-group --auto-scaling-group-name $ASG_NAME --launch-configuration-name $LC_NAME --min-size $EC2_MIN_INSTANCES --max-size $EC2_MAX_INSTANCES --load-balancer-names $ELB_NAME --availability-zones eu-west-1a eu-west-1b eu-west-1c --health-check-type ELB --health-check-grace-period 300 --tags Key=Name,Value=$TAG"
      	echo "$CMD" >> "$LOG_FILE"
   	 	OUTPUT=$(aws autoscaling create-auto-scaling-group --auto-scaling-group-name $ASG_NAME --launch-configuration-name $LC_NAME --min-size $EC2_MIN_INSTANCES --max-size $EC2_MAX_INSTANCES --load-balancer-names $ELB_NAME --availability-zones eu-west-1a eu-west-1b eu-west-1c --health-check-type ELB --health-check-grace-period 300 --tags "Key=Name,Value=$TAG")
    	# [--desired-capacity <value>]
    	# [--default-cooldown <value>]
    	# [--termination-policies <value>]
    	# [--new-instances-protected-from-scale-in | --no-new-instances-protected-from-scale-in]
    	echo "$OUTPUT" >> "$LOG_FILE"
        ASG=$(get_value "$OUTPUT" "AutoScalingGroupARN")    
    fi
    echo "       Auto Scaling Group: $ASG" 

	aws autoscaling resume-processes --auto-scaling-group-name $ASG_NAME

    OUTPUT=$(aws autoscaling describe-auto-scaling-instances)
    search_values "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    i=0
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "       EC2 Instance: $INSTANCE_ID"
	    i=$((i+1))
    done    

    echo

    times=0
    while [ 5 -gt $times ] && [[ -z $(aws ec2 describe-instances --instance-id $INSTANCE_ID --filters "Name=instance-state-code,Values=16" | grep "running") ]]
    do
        times=$(( $times + 1 ))
        echo Check if $INSTANCE_ID is running [$times]...
        sleep 5s
    done

    if [ 5 -eq $times ]; then
        echo EC2 Instance $INSTANCE_ID is not running. Exiting...
        echo
        exit    
    fi

    OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG" "Name=instance-state-name,Values=running")
    search_values "$OUTPUT" "InstanceId"
    i=0
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "       EC2 Instance: $INSTANCE_ID [running]"
	    i=$((i+1))
    done    

    echo 
    echo "$i WordPress instances up and running."   
    echo

# ----------- UPDATE -----------
# Update WordPress by replacing all ec2 instances in your AWS account via AWS CLI.
elif [[ $ACTION == "update" ]]; then
    echo "----- UPDATE WORDPRESS -----" >> "$LOG_FILE"
    STEP=0
    STEPS=4

   	# ----- CHECK LAUNCH CONFIGURATIONS -----
   	STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Launch Configuration..."
    OUTPUT=$(aws autoscaling describe-launch-configurations)
    LC=$(search_value "$OUTPUT" "LaunchConfigurationName" "LaunchConfigurationName" "$LC_NAME")
    if [[ -z $LC ]]; then
        echo "      ERROR: No Launch Configuration found. Please use \"manage-wordpress create\" instead."
        echo
        exit
    fi
    echo "      Launch Configuration: $LC" 

   	# ----- CHECK AUTO SCALING GROUPS -----
    STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Check Auto Scaling Group..."
    OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)
    ASG=$(search_value "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    if [[ -z $ASG ]]; then    
        echo "      ERROR: No Auto Scaling Group found. Please use \"manage-wordpress create\" instead."
        echo
        exit  
    fi
    echo "      Auto Scaling Group: $ASG" 

   	# ----- DELETE EC2 INSTANCES IN AUTO SCALING GROUP -----
    STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Searching Auto Scaling Instances..."
    OUTPUT=$(aws autoscaling describe-auto-scaling-instances)
    search_values "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    i=0
    for INSTANCE_ID in "${search_array[@]}"
    do
        echo "      Deleting Auto Scaling Instance \"$INSTANCE_ID\"..."
        OUTPUT=$(run_cmd "aws ec2 terminate-instances --instance-ids $INSTANCE_ID")
        i=$((i+1))
    done 
    echo "      $i Auto Scaling Instances deleted" 
    echo

    sleep 10s

   	# ----- WAIT UNTIL INSTANCES ARE REALLY TERMINATED -----
    # EC2 state-codes: pending=0, running=16, shutting-down=32, terminated=48, stopping=64, stopped=80
    times=0
    while [ 10 -gt $times ] && [[ -z $(aws ec2 describe-instances --instance-id $INSTANCE_ID --filters "Name=instance-state-code,Values=48" | grep "terminated") ]]
    do
        times=$(( $times + 1 ))
        echo "      Check if $INSTANCE_ID is terminated [$times]..."
        sleep 10s
    done

    if [ 10 -eq $times ]; then
        echo "      EC2 Instance $INSTANCE_ID is still running. Please re-run update in a minute..."
        echo
        exit    
    fi

	echo "      Detaching Auto Scaling Instance \"$INSTANCE_ID\"..."
	OUTPUT=$(run_cmd "aws autoscaling detach-instances --instance-ids $INSTANCE_ID --auto-scaling-group-name $ASG_NAME --no-should-decrement-desired-capacity")

   	# ----- CREATE EC2 INSTANCES IN AUTO SCALING GROUP -----
    STEP=$((STEP+1))
    echo "[$STEP/$STEPS] Creating new Auto Scaling Instances..."

	aws autoscaling resume-processes --auto-scaling-group-name $ASG_NAME

    sleep 5s          

    OUTPUT=$(aws autoscaling describe-auto-scaling-instances) 
    search_values "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    i=0
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "      EC2 Instance: $INSTANCE_ID"
	    i=$((i+1))
    done    

    echo

    times=0
    while [ 5 -gt $times ] && [[ -z $(aws ec2 describe-instances --instance-id $INSTANCE_ID --filters "Name=instance-state-code,Values=16" | grep "running") ]]
    do
        times=$(( $times + 1 ))
        echo Check if $INSTANCE_ID is running [$times]...
        sleep 5s
    done

    if [ 5 -eq $times ]; then
        echo EC2 Instance $INSTANCE_ID is not running. Exiting...
        echo
        exit    
    fi

    OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG" "Name=instance-state-name,Values=running")
    search_values "$OUTPUT" "InstanceId"
    i=0
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "      EC2 Instance: $INSTANCE_ID [running]"
	    i=$((i+1))
    done    

    echo 
    echo "$i WordPress instances updated and up and running again."   
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
        STEPS=12
        STEP=0

        # ----- STOP AUTO SCALING -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Auto Scaling Group..."
    	OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)
    	ASG=$(search_value "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    	if [[ -n $ASG ]]; then    
		    echo "       Setting max instances to 0 for Auto Scaling Group \"$ASG\"..."     
            run_cmd "aws autoscaling update-auto-scaling-group --auto-scaling-group-name $ASG_NAME --max-size 0 --min-size 0"
		else
            echo "       No Auto Scaling Group found"        
		fi

        # ----- DELETE INSTANCES -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Auto Scaling Instances..."
    	OUTPUT=$(aws autoscaling describe-auto-scaling-instances)
    	search_values "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    	i=0
    	for INSTANCE_ID in "${search_array[@]}"
    	do
	    	# echo "       Detaching Auto Scaling Instance \"$INSTANCE_ID\"..."
	        # run_cmd "aws autoscaling detach-instances --instance-ids $INSTANCE_ID --auto-scaling-group-name $ASG_NAME --should-decrement-desired-capacity"

	    	echo "       Deleting Auto Scaling Instance \"$INSTANCE_ID\"..."
	        run_cmd "aws ec2 terminate-instances --instance-ids $INSTANCE_ID"
	   		i=$((i+1))
    	done    
 		echo "       $i Auto Scaling Instances deleted"  

        # ----- DELETE LAUNCH CONFIGURATIONS -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Launch Configurations..."
    	OUTPUT=$(aws autoscaling describe-launch-configurations)
	    LC=$(search_value "$OUTPUT" "LaunchConfigurationName" "LaunchConfigurationName" "$LC_NAME")
    	if [[ -n $LC ]]; then    
		    echo "       Deleting Launch Configuration \"$LC\"..."     
            run_cmd "aws autoscaling delete-launch-configuration --launch-configuration-name $LC_NAME"
		else
            echo "       No Launch Configurations found"        
		fi

        # ----- DELETE CLOUD WATCH -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching CloudWatch Alarms..."
		OUTPUT=$(aws cloudwatch describe-alarms --alarm-name-prefix $TAG)	
    	search_values "$OUTPUT" "AlarmName" "MetricName" "CPUUtilization"
    	i=0
    	for ALARM_NAME in "${search_array[@]}"
    	do
	  		echo "       Deleting CloudWatch Alarm \"$ALARM_NAME\"..."
			run_cmd "aws cloudwatch delete-alarms --alarm-name $ALARM_NAME"
	    	i=$((i+1))
    	done    
    
		echo "       $i CloudWatch Alarms deleted"

        # ----- DELETE SNS TOPICS -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Simple Notification Service Topics (SNS)..."
        OUTPUT=$(aws sns list-topics)	
        search_values "$OUTPUT" "TopicArn" "TopicArn" "$TAG"
        i=0
        for SNS_ARN in "${search_array[@]}"
        do
	  		echo "       Deleting Notification Topic (SNS) \"$SNS_ARN\"... -> not implemented yet!"
            run_cmd "aws sns delete-topic --topic-arn $SNS_ARN"
            i=$((i+1))
        done    

		echo "       $i Notification Topics (SNS) deleted"

        # ----- DELETE AUTO SCALING GROUP -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Auto Scaling Group..."
    	OUTPUT=$(aws autoscaling describe-auto-scaling-groups --auto-scaling-group-name $ASG_NAME)
    	ASG=$(search_value "$OUTPUT" "AutoScalingGroupARN" "AutoScalingGroupName" "$ASG_NAME")
    	if [[ -n $ASG ]]; then    
		    echo "       Deleting Auto Scaling Group \"$ASG_NAME\"..."     
            run_cmd "aws autoscaling delete-auto-scaling-group --auto-scaling-group-name $ASG_NAME --force-delete"
		else
            echo "       No Auto Scaling Group found"        
		fi

        # ----- DELETE EC2 INSTANCES -----
	   	# STEP=$((STEP+1))
        # echo "[$STEP/$STEPS] Searching remaining EC2 instances..."
        # OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG") 
        # search_values "$OUTPUT" "InstanceId"
 		# i=0
        # for INSTANCE_ID in "${search_array[@]}"
        # do
        # 	# check if instance is already terminated
        # 	#"Name": "running"
	    #     #STATE=$(search_value "$OUTPUT" "Name" "InstanceId" "$INSTANCE_ID")
        # 	
	    #    echo "       Deleting EC2 Instance \"$INSTANCE_ID\"..."
	    #    run_cmd "aws ec2 terminate-instances --instance-ids $INSTANCE_ID"
        #    i=$((i+1))
	    # done 
 		# echo "       $i EC2 Instances deleted"  

        # ----- DELETE ELB -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Elastic Loadbalancer..."
        OUTPUT=$(aws elb describe-load-balancers)
        ELB=$(search_value "$OUTPUT" "LoadBalancerName" "LoadBalancerName" "$ELB_NAME")
    	if [[ -n $ELB ]]; then    
		    echo "       Deleting Elastic Loadbalancer \"$ELB\"..."     
            run_cmd "aws elb delete-load-balancer --load-balancer-name $ELB_NAME"
		else
            echo "       No Elastic Loadbalancer found"        
		fi

        OUTPUT=$(aws iam list-server-certificates)
        ARN=$(search_value "$OUTPUT" "Arn" "ServerCertificateName" "$TAG")

        if [[ -n $ARN ]]; then
            echo "       Deleting Elastic Loadbalancer certificate..."
            run_cmd "aws iam delete-server-certificate --server-certificate-name=$TAG"
        else
             echo "       No Elastic Loadbalancer certificate found"
        fi

        # ----- DELETE Route53 -----
        STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Route53 host zone..."
        OUTPUT=$(aws route53 list-hosted-zones-by-name --dns-name $DOMAIN_NAME)
        ZONE_ID=$(search_value "$OUTPUT" "Id")
        if [[ -n $ZONE_ID ]]; then
            OUTPUT=$(aws route53 list-resource-record-sets --hosted-zone-id $ZONE_ID)
            ELB_ID=$(search_value "$OUTPUT" "HostedZoneId")
            ELB_DNS=$(search_value "$OUTPUT" "DNSName")
            echo "       Deleting host zone \"$ZONE_ID\"..."
            echo "{ \"Changes\": [ { \"Action\": \"DELETE\", \"ResourceRecordSet\": { \"Name\": \"$DOMAIN_NAME\", \"Type\": \"A\", \"AliasTarget\": { \"HostedZoneId\": \"$ELB_ID\", \"DNSName\": \"$ELB_DNS\", \"EvaluateTargetHealth\": false } } }, { \"Action\": \"DELETE\", \"ResourceRecordSet\": { \"Name\": \"www.$DOMAIN_NAME\", \"Type\": \"A\", \"AliasTarget\": { \"HostedZoneId\": \"$ELB_ID\", \"DNSName\": \"$ELB_DNS\", \"EvaluateTargetHealth\": false } } } ] }" > change-resource-record-sets.json
            run_cmd "aws route53 change-resource-record-sets --hosted-zone-id $ZONE_ID --change-batch file://change-resource-record-sets.json"
            rm change-resource-record-sets.json
            run_cmd "aws route53 delete-hosted-zone --id $ZONE_ID"
        else
            echo "       No host zone found"        
        fi


        # ----- DELETE RDS -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching RDS Database..."
        OUTPUT=$(aws rds describe-db-instances)
        DB=$(search_value "$OUTPUT" "DBInstanceIdentifier" "DBInstanceIdentifier" "$DB_NAME")
        if [[ -n $DB ]]; then
            echo "       Deleting RDS Database \"$DB\"..."
            run_cmd "aws rds delete-db-instance --db-instance-identifier $DB_NAME --skip-final-snapshot"
        else
            echo "       No RDS Database found"        
        fi

        # ----- DELETE CLOUDFRONT -----
        STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching CloudFront instance..."
        # enable AWC CLI preview mode for CloudFront Support
        aws configure set preview.cloudfront true
        OUTPUT=$(aws cloudfront list-distributions)
        CF_ID=$(get_value "$OUTPUT" "ARN")
        if [[ -n $CF_ID ]]; then
            echo "       Deleting CloudFront \"$CF_ID\"..."
            CF_ID=${CF_ID##*/}
            CONFIG=$(aws cloudfront get-distribution-config --id $CF_ID)
            ETAG=$(get_value "$CONFIG" "ETag")
            ENABLED=$(get_value "$CONFIG" "Enabled")

            if [[ $ENABLED == "true" ]]; then
                echo "CloudFront get's disabled. Deleting will happen on second run."
                echo "$CONFIG" >> "$TAG-cloudflare-disable.json"
                tail -n +4 $TAG-cloudflare-disable.json > $TAG-cloudflare-disable.json.tmp && mv $TAG-cloudflare-disable.json.tmp $TAG-cloudflare-disable.json
                head -n -1 $TAG-cloudflare-disable.json > $TAG-cloudflare-disable.json.tmp && mv $TAG-cloudflare-disable.json.tmp $TAG-cloudflare-disable.json
                sed -i -e 's/true/false/g' $TAG-cloudflare-disable.json
                sed -i.old '1s;^;{\n;' $TAG-cloudflare-disable.json

                $(run_cmd "aws cloudfront update-distribution --id $CF_ID --distribution-config file://$TAG-cloudflare-disable.json --if-match $ETAG")
                rm $TAG-cloudflare-disable.json
            else
                CF_STATUS=$(get_value "$OUTPUT" "Status")
                if [[ $CF_STATUS == "Deployed" ]]; then
                    $(run_cmd "aws cloudfront delete-distribution --id $CF_ID --if-match $ETAG")
                else
                    echo "CloudFront can't get deleted right now while the status is $CF_STATUS"
                fi
            fi
        else
            echo "      No CloudFront found"
        fi

        # ----- DELETE S3 -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching S3 Bucket..."
        OUTPUT=$(aws s3api list-buckets)
        S3_BUCKET=$(search_value "$OUTPUT" "Name" "Name" "$S3_BUCKET_NAME")
        if [[ -n $S3_BUCKET ]]; then
		    S3_URL=$(get_s3_url $S3_BUCKET_NAME)
            echo "       Deleting S3 Bucket \"$S3_URL\"..."
            run_cmd "aws s3 rm s3://$S3_BUCKET_NAME --recursive"
            run_cmd "aws s3api delete-bucket --bucket $S3_BUCKET_NAME"
        else
            echo "       No S3 Bucket found"        
        fi

        # ----- DELETE IAM User -----
        STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching IAM user..."
        OUTPUT=$(aws iam list-users)
        HAS_USER=$(search_value "$OUTPUT" "UserName" "UserName" "$IAM_USER")
        if [[ -n $HAS_USER ]]; then
            echo "       Deleting IAM user \"$IAM_USER\"..."

            OUTPUT=$(aws iam list-access-keys --user-name $IAM_USER)
            search_values "$OUTPUT" "AccessKeyId" "UserName" "$IAM_USER"
            i=0
            for ACCESS_KEY in "${search_array[@]}"
            do
                run_cmd "aws iam delete-access-key --access-key $ACCESS_KEY --user-name $IAM_USER"
            done

            run_cmd "aws iam detach-user-policy --user-name $IAM_USER --policy-arn arn:aws:iam::aws:policy/AmazonS3FullAccess"
            run_cmd "aws iam delete-user --user-name $IAM_USER"
        else
            echo "       No IAM User found"        
        fi
      

        # ----- DELETE SECURITY GROUP -----
	   	STEP=$((STEP+1))
        echo "[$STEP/$STEPS] Searching Security Group..."
        OUTPUT=$(aws ec2 describe-security-groups)
        SEC_GROUP_ID=$(search_value "$OUTPUT" "GroupId" "GroupName" "$SEC_GROUP_NAME")
        if [[ -n $SEC_GROUP_ID ]]; then
            echo "       Deleting Security Group \"$SEC_GROUP_ID\"..."
            run_cmd "aws ec2 delete-security-group --group-id $SEC_GROUP_ID"
        else
            echo "       No Security Group found"        
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
    echo "Network & Domains"
    echo "   Security Group Name:        $SEC_GROUP_NAME"
    echo "   Security Group Description: $SEC_GROUP_DESC"
    echo "   VPC IP Block:               $VPC_IP_BLOCK"
    echo "   Domain:                     $DOMAIN_NAME"
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

    # ----- LIST DOMAIN -----
    if [[ -n $DOMAIN_NAME ]]; then
        OUTPUT=$(aws route53 list-hosted-zones)
        ZONE_ID=$(search_value "$OUTPUT" "Id" "Name" "$DOMAIN_NAME.")
        if [[ -z $ZONE_ID ]]; then
            ZONE_ID="none"
        fi
        echo "   Hosted Zone:                $ZONE_ID ($DOMAIN_NAME)"
    fi

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

    # ----- LIST CLOUDFRONT -----
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

    # ----- LIST SNS TOPICS -----
	OUTPUT=$(aws sns list-topics)	
    search_values "$OUTPUT" "TopicArn" "TopicArn" "$TAG"
    i=0
    for SNS_ARN in "${search_array[@]}"
    do
	    echo "   Notification Topic (SNS):   $SNS_ARN"
	    i=$((i+1))
    done    
    
	echo "   Notification Topics (SNS):  $i" 

    # ----- LIST CLOUD WATCH -----
	OUTPUT=$(aws cloudwatch describe-alarms --alarm-name-prefix $TAG)	
    search_values "$OUTPUT" "AlarmName" "MetricName" "CPUUtilization"
    i=0
    for ALARM_NAME in "${search_array[@]}"
    do
	    echo "   CloudWatch Alarm:           $ALARM_NAME"
	    i=$((i+1))
    done    
    
	echo "   CloudWatch Alarms:          $i" 

    # ----- LIST INSTANCES -----
    OUTPUT=$(aws autoscaling describe-auto-scaling-instances)
    search_values "$OUTPUT" "InstanceId" "LaunchConfigurationName" "$LC_NAME"
    i=0
    for INSTANCE_ID in "${search_array[@]}"
    do
	    echo "   Auto Scaling Instance:      $INSTANCE_ID"
	    i=$((i+1))
    done    
    
	echo "   Auto Scaling Instances:     $i" 	
	echo 
    
# ----------- CONSOLE -----------
# Print console output of first EC2 instance found.
elif [[ $1 == "console" ]]; then

    OUTPUT=$(aws ec2 describe-instances --filters "Name=tag-value,Values=$TAG" "Name=instance-state-name,Values=running") 
    INSTANCE_ID=$(search_value "$OUTPUT" "InstanceId")
    if [[ -n $INSTANCE_ID ]]; then
	    OUTPUT=$(aws ec2 get-console-output --instance-id $INSTANCE_ID)
	    echo $OUTPUT
	else
	    echo "No EC2 instances found!"
    fi    
    
	echo    
    
# ----------- CONFIG -----------
# Create a new config file with as TAG.ini.
elif [[ $1 == "config" ]]; then

    INI_FILE="$TAG.ini"
    echo "Creating file $INI_FILE ..."
    echo

# has to be replaced with $INI_FILE, but "cat >$INI_FILE <<EOL" does not work"
# cat >pleskwp.cfg <<EOL
# AMI '${AMI}'
# REGION '${REGION}'
# INSTANCE_TYPE '${INSTANCE_TYPE}'
# VPC_IP_BLOCK '${VPC_IP_BLOCK}'
# DB_INSTANCE_TYPE '${DB_INSTANCE_TYPE}'
# DB_ENGINE '${DB_ENGINE}'
# DB_USERNAME '${DB_USERNAME}'
# DB_PASSWORD '${DB_PASSWORD}'
# EC2_MIN_INSTANCES '${EC2_MIN_INSTANCES}'
# NEWRELIC_KEY '${NEWRELIC_KEY}'
# NEWRELIC_NAME '${NEWRELIC_NAME}'
# WORDPRESS_TITLE '${WORDPRESS_TITLE}'
# WORDPRESS_DB_PREFIX '${WORDPRESS_DB_PREFIX}'
# WORDPRESS_USER_NAME '${WORDPRESS_USER_NAME}'
# WORDPRESS_USER_PASSWORD '${WORDPRESS_USER_PASSWORD}'
# WORDPRESS_USER_EMAIL '${WORDPRESS_USER_EMAIL}'
# DOMAIN_NAME '${DOMAIN_NAME}'
# ---END-OF-FILE---
# EOL

    cat $INI_FILE

	echo    

# ----------- HELP -----------
# Help page        
else 
    echo "Plesk WordPress Scaler automates provisioning of a highly available and auto-scaling WordPress to AWS."
    echo
    echo "Commands:"
    echo "   manage_wordpress.sh create  [TAG]      Create a new WordPress in your AWS account via AWS CLI."
    echo "   manage_wordpress.sh update  [TAG]      Recreate all EC2 instances with latest Docker image but keep RDS, S3, etc. as they are."
    echo "   manage_wordpress.sh delete  [TAG]      Delete the WordPress incl. database etc BE CAREFUL - this deletes all that the script created before."
    echo "   manage_wordpress.sh list    [TAG]      List settings and WordPress instances."
    echo "   manage_wordpress.sh console [TAG]      Print console output of first EC2 instance found."
    echo "   manage_wordpress.sh config  [TAG]      Create a new config file with as TAG.ini."
    echo
    echo "Parameters:"
    echo "   COMMAND TAG (optional)                 Name of the WordPress stack. Can only be small letters and numbers. Loads settings from TAG.ini file."
    echo "   delete OK                              Deletes WordPress stack without confirmation"
	print_footer
fi
