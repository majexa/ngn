<?php

q("ALTER TABLE sessions ADD userId INT(11) NULL, ADD INDEX (userId)");