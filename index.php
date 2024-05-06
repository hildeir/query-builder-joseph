<?php
require "autoload.php"; //autoload do querybuilder

/****** aqui vai o uso do query builder, abaixo é um exemplo de como usar *******/
use \models\tabela;

$r = tabela::select(["coluna1","coluna2"])->get();