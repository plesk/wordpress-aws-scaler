#!/bin/bash

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
		fi
	done < wordpress-scalar.ini
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

# Set values from configuration file
SEC_GROUP_NAME=$(get_config SEC_GROUP_NAME)
SEC_GROUP_DESC=$(get_config SEC_GROUP_DESC)
AMI=$(get_config AMI)
INSTANCE_TYPE=$(get_config INSTANCE_TYPE)
REGION=$(get_config REGION)

if [[ $1 == "create" ]]; then

    echo "[1/5] Creating VPC..."
    # sample output: { "Vpc": { "VpcId": "vpc-xxxxxxxx", "InstanceTenancy": "default", "State": "pending", "DhcpOptionsId": "dopt-xxxxxxxx", "CidrBlock": "172.31.0.0/16", "IsDefault": false } }    
    output=$(aws ec2 create-vpc --cidr-block 172.31.0.0/16)
    VPC_ID=$(parse_json VpcId "$output")
    echo "VPC ID: $VPC_ID"
    
    echo "[2/5] Creating Security Group..."
    # sample output: { "GroupId": "sg-xxxxxxxxx" }       
    output=$(aws ec2 create-security-group --group-name $SEC_GROUP_NAME --description "$SEC_GROUP_DESC" --vpc-id $VPC_ID)
    SEC_GROUP_ID=$(parse_json GroupId "$output")    
    echo "Security Group Id: $SEC_GROUP_ID"
        
    echo "[3/5] Creating EC2 instances..."        
    #output=$(aws ec2 run-instances --cli-input-json file://ec2-config.json)
    output=$(aws ec2 run-instances --image-id $AMI --instance-type $INSTANCE_TYPE --group $SEC_GROUP_ID --region $REGION --key $EC2_INSTANCE_KEY --block-device-mapping "/dev/sda1=:16:true" --instance-initiated-shutdown-behavior stop --user-data-file ec2-user-data.sh)
    echo "OUT $output"
 
           --image-id <value>
          [--key-name <value>]
          [--security-groups <value>]
          [--security-group-ids <value>]
          [--user-data <value>]
          [--instance-type <value>]
          [--placement <value>]
          [--kernel-id <value>]
          [--ramdisk-id <value>]
          [--block-device-mappings <value>]
          [--monitoring <value>]
          [--subnet-id <value>]
          [--disable-api-termination | --enable-api-termination]
          [--instance-initiated-shutdown-behavior <value>]
          [--private-ip-address <value>]
          [--client-token <value>]
          [--additional-info <value>]
          [--network-interfaces <value>]
          [--iam-instance-profile <value>]
          [--ebs-optimized | --no-ebs-optimized]
          [--count <value>]
          [--secondary-private-ip-addresses <value>]
          [--secondary-private-ip-address-count <value>]
          [--associate-public-ip-address | --no-associate-public-ip-address]   

    echo ""
    echo "WordPress instances up and running."   
    
elif [[ $1 == "delete" ]]; then
    echo -e "Please enter OK: \c "
    read  ok
    if [ $ok = "OK" ]; then

        echo "[1/5] Deleting EC2 instances..."

        echo "[2/5] Deleting Security Group..."
        # TODO: search for existing security groups to get correct VpcId
        output=$(aws ec2 describe-security-groups --group-names $SEC_GROUP_NAME)
        # "VpcId": "vpc-xxxxxxxx"
        aws ec2 delete-security-group --group-id $SEC_GROUP_ID

        echo "[3/5] Deleting VPC..."
        aws ec2 delete-vpc --vpc-id $VPC_ID
    
        echo ""
        echo "WordPress and all depending data deleted."   
    fi

else 
    echo "##################################"
    echo "# Plesk WordPress Scaler for AWS #"
    echo "##################################"
    echo ""
    echo "Commands:"
    echo "   manage_wordpress.sh create        to create a new WordPress in your AWS account via AWS CLI."
    echo "   manage_wordpress.sh delete        to delete the WordPress incl. database etc BE CAREFUL - this deletes all that the script created before."
    echo ""
fi
