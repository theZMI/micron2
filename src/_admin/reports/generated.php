<?php

if (!isset($tasksByGroups)) {
    throw new RuntimeException("Необходимо передать параметр tasks");
}