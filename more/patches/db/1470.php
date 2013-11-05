<?

foreach (DdCore::tables() as $table) {
  q("ALTER TABLE $table DROP datePublish");
  q("ALTER TABLE $table DROP commentsUpdate");
}
