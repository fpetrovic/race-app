#!/bin/bash

# This script will generate a CSV file with 40,000 rows based on a predefined template.

OUTPUT_FILE="tests/output.csv"

firstNames=("John" "Jane" "Michael" "Emily" "Chris" "Amanda" "David" "Rebecca" "Alex" "Sophia")
lastNames=("Smith" "Johnson" "Brown" "Williams" "Jones" "Garcia" "Miller" "Davis" "Rodriguez" "Martinez")
ageCategory=("M18-25", "M26-34", "M35-43", "F18-25", "F26-34", "F35-43")

# Create the header
echo "fullName,distance,time,ageCategory" > $OUTPUT_FILE

# Generate rows based on the template
for i in $(seq 1 20000); do
	randomNumber=$((RANDOM % 999999 + 1))
	randomFirstName=${firstNames[$RANDOM % ${#firstNames[@]}]}
    randomLastName=${lastNames[$RANDOM % ${#lastNames[@]}]}
    randomAgeCategory=${ageCategory[$RANDOM % ${#ageCategory[@]}]}

    if (( i % 2 == 0 )); then
        distanceType="long"
        timeRange=$((RANDOM % 181 + 240))
    else
        distanceType="medium"
        timeRange=$((RANDOM % 181 + 120))
    fi

	hours=$(($timeRange / 60))
    minutes=$(($timeRange % 60))
    seconds=$((RANDOM % 60))

    formattedMinutes=$(printf "%02d" $minutes)
    formattedSeconds=$(printf "%02d" $seconds)

    echo "${randomFirstName} ${randomLastName} ${randomNumber},${distanceType},${hours}:${formattedMinutes}:${formattedSeconds}, ${randomAgeCategory}" >> $OUTPUT_FILE
done

echo "CSV file generated as $OUTPUT_FILE"
