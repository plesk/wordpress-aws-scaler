[![Apache 2](http://img.shields.io/badge/license-Apache%202-blue.svg)](http://www.apache.org/licenses/LICENSE-2.0)

# WordPress AWS Scaler

This is a how-to including scripts about how to auto-scale WordPress across multiple EC2 instances on AWS with using Docker for deploying the WordPress core, RDS for the managed MySQL database server and CloudFront with S3 for delivering static files.

# Requirements
 
 * Docker (installed on your development machine)
 * AWS account 
 
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