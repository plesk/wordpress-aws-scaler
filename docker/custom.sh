#!/bin/bash
echo "hello $WORDPRESS_URL"
DOMAIN_NO_PROTOCOL=$(echo "$WORDPRESS_URL" | sed 's~http[s]*://~~g')
echo "$DOMAIN_NO_PROTOCOL"

wp search-replace http://local.wordpress.dev "$WORDPRESS_URL" --recurse-objects --allow-root
if [[ -n $DOMAIN_NO_PROTOCOL ]]; then
	wp search-replace local.wordpress.dev "$DOMAIN_NO_PROTOCOL" --recurse-objects --allow-root
fi