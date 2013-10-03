<?php

q("ALTER TABLE tagItems ENGINE MyISAM");
q("ALTER IGNORE TABLE tagItems ADD UNIQUE INDEX dupIdx (groupName, strName, tagId, itemId, collection)");
q("ALTER TABLE  tagItems ENGINE InnoDB");
