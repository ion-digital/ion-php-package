#!/usr/bin/env sh

SCRIPT=$1
if [ $SCRIPT = "" ]; then
   SCRIPT="builds"
fi

 composer run-script $SCRIPT
