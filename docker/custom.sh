#!/bin/bash

wp search-replace http://local.wordpress.dev "$WORDPRESS_URL" --recurse-objects --allow-root