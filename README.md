[![Apache 2](http://img.shields.io/badge/license-Apache%202-blue.svg)](http://www.apache.org/licenses/LICENSE-2.0)

# WordPress AWS Scaler

This is a how-to including scripts about how to auto-scale WordPress across multiple EC2 instances on AWS with using Docker for deploying the WordPress core, RDS for the managed MySQL database server and CloudFront with S3 for delivering static files.

# Requirements
 
 * Docker (installed on your development machine)
 * AWS account 
 * AWS CLI see https://docs.aws.amazon.com/cli/latest/userguide/installing.html 

# Prepare AWS CLI on developer machine
 
 * Install AWS CLI

    $ sudo pip install awscli

 * Configure AWS CLI

    $ aws configure

 * Check if all works by listing existing ec2 instances

    $ aws ec2 describe-instances

 * You can later create new WordPress instances by creating pre-configured ec2 instances

    $ aws ec2 run-instances --cli-input-json file://ec2-config.json

# How to build and deploy WordPress on AWS

# Build the docker image for WordPress

    $ docker build -t janloeffler/wordpress-aws-scaler:0.1 .

# Check that our docker image works

    $ docker run -p 80:80 -it janloeffler/wordpress-aws-scaler:0.1

Visit [http://localhost/](http://localhost/)! Stop your server with **Ctrl+C**.

# Upload docker image to registry

    $ docker push janloeffler/wordpress-aws-scaler:0.1

# Setup MySQL database with RDS

Select a database type in AWS RDS:
   * Amazon Aurora (high performance, works only on new DB.r3 instances)
   * MySQL DB
   * Maria DB
 
Choose a EC2 resource type: e.g. db.t2.small (1 vCPU, 1 GiB RAM)
 
Select "Multi-AZ Deployment" to have Amazon RDS maintain a synchronous standby replica in a different Availability Zone than the DB instance. Amazon RDS will automatically fail over to the standby in the case of a planned or unplanned outage of the primary. Learn More at http://docs.aws.amazon.com/AmazonRDS/latest/UserGuide/Concepts.MultiAZ.html

# Create ELB and AutoScaling group

tbd

# AWS CLI Calls by manage_wordpress.sh script

    $ manage_wordpress.sh list
    
    $ aws ec2 describe-vps
    $ aws ec2 describe-security-groups
    $ aws ec2 describe-instances

    $ manage_wordpress.sh create

    $ aws ec2 describe-vps
    $ aws ec2 create-vpc
    $ aws ec2 describe-security-groups
    $ aws ec2 create-security-group
    $ aws ec2 describe-instances
    $ aws ec2 run-instances
    
    $ manage_wordpress.sh delete
    
    $ aws ec2 terminate-instances
    $ aws ec2 delete-security-group
    $ aws ec2 delete-vpc
    
# AWS CLI APIs

    $ aws autoscaling    
    $ aws ec2
    $ aws cloudfront
    $ aws cloudwatch
    $ aws elb
    $ aws elbv2
    $ aws rds
    $ aws s3
    $ aws route53
    $ aws events
    
# All commands of AWS EC2 CLI

    $ accept-vpc-peering-connection
    $ allocate-address
    $ allocate-hosts
    $ assign-private-ip-addresses
    $ associate-address
    $ associate-dhcp-options
    $ associate-route-table
    $ attach-classic-link-vpc
    $ attach-internet-gateway
    $ attach-network-interface
    $ attach-volume
    $ attach-vpn-gateway
    $ authorize-security-group-egress
    $ authorize-security-group-ingress
    $ bundle-instance
    $ cancel-bundle-task
    $ cancel-conversion-task
    $ cancel-export-task
    $ cancel-import-task
    $ cancel-reserved-instances-listing
    $ cancel-spot-fleet-requests
    $ cancel-spot-instance-requests
    $ confirm-product-instance
    $ copy-image
    $ copy-snapshot
    $ create-customer-gateway
    $ create-dhcp-options
    $ create-flow-logs
    $ create-image
    $ create-instance-export-task
    $ create-internet-gateway
    $ create-key-pair
    $ create-nat-gateway
    $ create-network-acl
    $ create-network-acl-entry
    $ create-network-interface
    $ create-placement-group
    $ create-reserved-instances-listing
    $ create-route
    $ create-route-table
    $ create-security-group
    $ create-snapshot
    $ create-spot-datafeed-subscription
    $ create-subnet
    $ create-tags
    $ create-volume
    $ create-vpc
    $ create-vpc-endpoint
    $ create-vpc-peering-connection
    $ create-vpn-connection
    $ create-vpn-connection-route
    $ create-vpn-gateway
    $ delete-customer-gateway
    $ delete-dhcp-options
    $ delete-flow-logs
    $ delete-internet-gateway
    $ delete-key-pair
    $ delete-nat-gateway
    $ delete-network-acl
    $ delete-network-acl-entry
    $ delete-network-interface
    $ delete-placement-group
    $ delete-route
    $ delete-route-table
    $ delete-security-group
    $ delete-snapshot
    $ delete-spot-datafeed-subscription
    $ delete-subnet
    $ delete-tags
    $ delete-volume
    $ delete-vpc
    $ delete-vpc-endpoints
    $ delete-vpc-peering-connection
    $ delete-vpn-connection
    $ delete-vpn-connection-route
    $ delete-vpn-gateway
    $ deregister-image
    $ describe-account-attributes
    $ describe-addresses
    $ describe-availability-zones
    $ describe-bundle-tasks
    $ describe-classic-link-instances
    $ describe-conversion-tasks
    $ describe-customer-gateways
    $ describe-dhcp-options
    $ describe-export-tasks
    $ describe-flow-logs
    $ describe-host-reservation-offerings
    $ describe-host-reservations
    $ describe-hosts
    $ describe-id-format
    $ describe-identity-id-format
    $ describe-image-attribute
    $ describe-images
    $ describe-import-image-tasks
    $ describe-import-snapshot-tasks
    $ describe-instance-attribute
    $ describe-instance-status
    $ describe-instances
    $ describe-internet-gateways
    $ describe-key-pairs
    $ describe-moving-addresses
    $ describe-nat-gateways
    $ describe-network-acls
    $ describe-network-interface-attribute
    $ describe-network-interfaces
    $ describe-placement-groups
    $ describe-prefix-lists
    $ describe-regions
    $ describe-reserved-instances
    $ describe-reserved-instances-listings
    $ describe-reserved-instances-modifications
    $ describe-reserved-instances-offerings
    $ describe-route-tables
    $ describe-scheduled-instance-availability
    $ describe-scheduled-instances
    $ describe-security-group-references
    $ describe-security-groups
    $ describe-snapshot-attribute
    $ describe-snapshots
    $ describe-spot-datafeed-subscription
    $ describe-spot-fleet-instances
    $ describe-spot-fleet-request-history
    $ describe-spot-fleet-requests
    $ describe-spot-instance-requests
    $ describe-spot-price-history
    $ describe-stale-security-groups
    $ describe-subnets
    $ describe-tags
    $ describe-volume-attribute
    $ describe-volume-status
    $ describe-volumes
    $ describe-vpc-attribute
    $ describe-vpc-classic-link
    $ describe-vpc-classic-link-dns-support
    $ describe-vpc-endpoint-services
    $ describe-vpc-endpoints
    $ describe-vpc-peering-connections
    $ describe-vpcs
    $ describe-vpn-connections
    $ describe-vpn-gateways
    $ detach-classic-link-vpc
    $ detach-internet-gateway
    $ detach-network-interface
    $ detach-volume
    $ detach-vpn-gateway
    $ disable-vgw-route-propagation
    $ disable-vpc-classic-link
    $ disable-vpc-classic-link-dns-support
    $ disassociate-address
    $ disassociate-route-table
    $ enable-vgw-route-propagation
    $ enable-volume-io
    $ enable-vpc-classic-link
    $ enable-vpc-classic-link-dns-support
    $ get-console-output
    $ get-console-screenshot
    $ get-host-reservation-purchase-preview
    $ get-password-data
    $ help
    $ import-image
    $ import-key-pair
    $ import-snapshot
    $ modify-hosts
    $ modify-id-format
    $ modify-identity-id-format
    $ modify-image-attribute
    $ modify-instance-attribute
    $ modify-instance-placement
    $ modify-network-interface-attribute
    $ modify-reserved-instances
    $ modify-snapshot-attribute
    $ modify-spot-fleet-request
    $ modify-subnet-attribute
    $ modify-volume-attribute
    $ modify-vpc-attribute
    $ modify-vpc-endpoint
    $ modify-vpc-peering-connection-options
    $ monitor-instances
    $ move-address-to-vpc
    $ purchase-host-reservation
    $ purchase-reserved-instances-offering
    $ purchase-scheduled-instances
    $ reboot-instances
    $ register-image
    $ reject-vpc-peering-connection
    $ release-address
    $ release-hosts
    $ replace-network-acl-association
    $ replace-network-acl-entry
    $ replace-route
    $ replace-route-table-association
    $ report-instance-status
    $ request-spot-fleet
    $ request-spot-instances
    $ reset-image-attribute
    $ reset-instance-attribute
    $ reset-network-interface-attribute
    $ reset-snapshot-attribute
    $ restore-address-to-classic
    $ revoke-security-group-egress
    $ revoke-security-group-ingress
    $ run-instances
    $ run-scheduled-instances
    $ start-instances
    $ stop-instances
    $ terminate-instances
    $ unassign-private-ip-addresses
    $ unmonitor-instances
    $ wait