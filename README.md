# query-builder-joseph
#### Isto é um query builder feito com php para ser utilizado com o banco de dados mysql

### Configure o banco de dados
na pasta src abra o arquivo Config.php e configure o banco de dados:
~~~php
<?php
namespace src;

class Config{
    const DB_DRIVER = 'mysql'; //não altere 
    const DB_HOST = '127.0.0.1'; //localhost
    const DB_DATABASE = 'nome do seu banco de dados';

    const DB_USER = 'nome do usuário do banco';
    const DB_PASS = 'senha do usuario do banco';
}
~~~
### tabela do banco de dados a ser usada
para usar as tabelas do banco de dados acesse o diretório models e crie o arquivo em php com o nome da tabela do banco de dados e crie também a classe com o nome da tabela do banco 
de dados e extende ela, abaixo seguirá as etapas de como fazer: </br>
- primeiro crie o arquivo php dentro da pasta models como o nome da tabela do banco de dados: tabela.php </br>
- segunda etapa, dentro do arquivo tabela.php crie a classe com o nome da tabela do bando de dados e depois extende ela </br>
~~~php
<?php
namespace models;

use \src\QueryBuilder;

class tabela extends QueryBuilder{
}
~~~

### Utilize os comandos

### select:

* coluna = coluna da tabela
* valor = dados para coluna da tabela
* $r = o retorno da consulta em array
* tabela = é o nome da tabela do banco de dados, que foi criada dentro do diretório models
  
retorna todos resultados da tabela, sem especificar colunas: </br>
~~~php
use \models\tabela;
$r = tabela::select()→get();
~~~

retorna todo sresultados da tabela sem especificar colunas, usando uma condicão: </br>
~~~php
use \models\tabela;
$r = tabela::select()→where(“coluna”,valor)→get();
~~~
usando a clause and
~~~php
use \models\tabela;
$r = tabela::select()→where(“coluna”,valor)->and("coluna", valor)->get();
~~~
retorna todos resultados especificados: </br>
~~~php
use \models\tabela;
$r = tabela::select([“coluna1”,”coluna2”])→get();
~~~
retorna os resultados especificado com condição: </br>
~~~php
use \models\tabela;
$r = tabela:select([“coluna1”,”coluna2”])→where(“coluna”,valor)->get();
~~~

### insert:

insere dados na tabela: </br>
~~~
use \models\tabela;
tabela::insert([“coluna” => valor , ”coluna2” => valor])→execute();
~~~

### update:

update na tabela: </br>
~~~
use \models\tabela;
tabela::update()→set([“coluna” => valor , ”coluna2” => valor])→execute();
~~~

### inner join:
~~~
use \models\tabela1;
tabela1::select()->innerJoin(“tabela2”)->on(“tabela1.primarykey”,“tabela2.foreignkey”)→get();
~~~
~~~
use \models\tabela1;
tabela1::select([“tabela1.coluna”, “tabela2.coluna”])->innerJoin(“tabela2”)->on(“tabela1.primarykey”, “tabela2.foreignkey”)->where(“tabela1.coluna”, valor)→get();
~~~

### left join:
obs.: a "tabela2" não é necessário criar a classe no diretório models, apenas digite a tabela que deseja usar.
~~~
use \models\tabela1;
tabela1::select()->leftJoin(“tabela2”)->on(“tabela1.primarykey”,“tabela2.foreignkey”)→get();
~~~
~~~
use \models\tabela1;
tabela1::select([“tabela1.coluna”, “tabela2.coluna”])->leftJoin(“tabela2”)->on(“tabela1.primarykey”, “tabela2.foreignkey”)->where(“tabela1.coluna”, valor)→get();
~~~
#### Observações para o uso do join:
obs 1.: a "tabela2" utilizada no exemlo dos joins acima não é necessário criar a classe no diretório models, apenas digite o nome da tabela que deseja usar.</br>
obs 2.: caso deseja "pegar" as colunas na hora da consulta e as colunas das tabelas forem do mesmo nome deve usar um alias para a coluna, exemplo: </br>
~~~
tabela1::select([“tabela1.coluna as 'novo nome da coluna' ”, “tabela2.coluna”])->innerJoin(“tabela2”)->on(“tabela1.primarykey”, “tabela2.foreignkey”)->where(“tabela1.coluna”, valor)→get();
~~~
