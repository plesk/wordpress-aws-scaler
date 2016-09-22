#!/bin/bash
DOMAIN_NO_PROTOCOL=$(echo "$WORDPRESS_URL" | sed 's~http[s]*://~~g')
wp search-replace local.wordpress.dev "$DOMAIN_NO_PROTOCOL" --recurse-objects --allow-root