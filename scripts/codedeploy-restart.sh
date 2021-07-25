#!/bin/bash

set -eux

at now + 2 minute <<< $'service codedeploy-agent restart'