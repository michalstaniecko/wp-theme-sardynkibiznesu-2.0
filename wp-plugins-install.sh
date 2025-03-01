#!/bin/bash

# Sprawdź, czy plik CSV istnieje
if [ ! -f wp-plugins.csv ]; then
  echo "Plik wp-plugins.csv nie istnieje!"
  exit 1
fi

# Przeczytaj plik CSV i zainstaluj każdy plugin
while IFS=, read -r name status update version update_version auto_update; do
  if [ "$name" != "name" ]; then
    wp plugin install "$name" --version="$version" --allow-root
    if [ "$status" == "active" ]; then
      wp plugin activate "$name" --allow-root
    fi
    if [ "$update" == "yes" ]; then
      wp plugin update "$name" --version="$update_version" --allow-root
    fi
    if [ "$auto_update" == "yes" ]; then
      wp plugin auto-updates enable "$name" --allow-root
    else
      wp plugin auto-updates disable "$name" --allow-root
    fi
  fi
done < wp-plugins.csv
