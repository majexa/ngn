<?php

DdFieldCore::registerType('usersChatLog', [
  'dbType' => 'TEXT',
  'title' => 'Лог разговора пользователей',
  'order' => 400,
]);

class FieldEUsersChatLog extends FieldETextarea {
}