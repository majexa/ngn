<?php

q("ALTER TABLE dd_structures CHANGE name name VARCHAR(64)");
q("ALTER TABLE dd_fields CHANGE strName strName VARCHAR(64)");