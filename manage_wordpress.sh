#!/bin/bash

function parse_json()
{
echo $2
    echo $1 | \
    sed -e 's/[{}]/''/g' | \
    sed -e 's/", "/'\",\"'/g' | \
    sed -e 's/" ,"/'\",\"'/g' | \
    sed -e 's/" , "/'\",\"'/g' | \
    sed -e 's/","/'\"---SEPERATOR---\"'/g' | \
    awk -F=':' -v RS='---SEPERATOR---' "\$1~/\"$2\"/ {print}" | \
    sed -e "s/\"$2\"://" | \
    tr -d "\n\t" | \
    sed -e 's/\\"/"/g' | \
    sed -e 's/\\\\/\\/g' | \
    sed -e 's/^[ \t]*//g' | \
    sed -e 's/^"//'  -e 's/"$//'
}

if [[ $1 == "create" ]]; then
    echo "Creating VPC"
    #output=$(aws ec2 create-vpc --cidr-block 172.31.0.0/16)
    output="{ "Vpc": { "VpcId": "vpc-b67af3d2", "InstanceTenancy": "default", "State": "pending", "DhcpOptionsId": "dopt-31861654", "CidrBlock": "172.31.0.0/16", "IsDefault": false } }"
    echo "IN $output"
    output2=$(parse_json $output VpcId)
    echo "OUT $output2"
        
    echo "Creating EC2 instances"   
    #aws ec2 run-instances --cli-input-json file://ec2-config.json
    
elif [[ $1 == "remove" ]]; then
    echo -e "Please enter OK: \c "
    read  ok
    if [ $ok = "OK" ]; then
        echo "WordPress and all depending data deleted."   
    fi

else 
    echo "##################################"
    echo "# Plesk WordPress Scaler for AWS #"
    echo "##################################"
    echo ""
    echo "Run \"manage_wordpress.sh create\" to create a new WordPress in your AWS account via AWS CLI."
    echo "Run \"manage_wordpress.sh remove\" to delete the WordPress incl. database etc BE CAREFUL - this deletes all that the script created before."
fi

# AWS CLI commands

# aws ec2 create-key-pair
# aws ec2 delete-key-pair

# aws ec2 create-vpc
# aws ec2 delete-vpc

# aws ec2 create-security-group
# aws ec2 delete-security-group

# aws ec2 run-instances
# aws ec2 terminate-instances

# aws ec2 describe-instances