#!/bin/bash

# Create new m3.medium instance using ami-64385917 (Amazon Linux AMI 2016.03.e x86_64 ECS HVM GP2)
# using the default security group and a 10GB EBS datastore as /dev/sda1.
aws ec2 run-instances --cli-input-json file://ec2-config.json