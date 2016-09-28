#!/bin/bash

DOMAIN_NO_PROTOCOL=$(echo "$WORDPRESS_URL" | sed 's~http[s]*://~~g')
if [[ -z $DOMAIN_NO_PROTOCOL ]]; then
	wp search-replace local.wordpress.dev "$DOMAIN_NO_PROTOCOL" --recurse-objects --allow-root
fi