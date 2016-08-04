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

tbd

# Create ELB and AutoScaling group

tbd