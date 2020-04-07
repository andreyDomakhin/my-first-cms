<?php

/**
 * Класс для обработки пользователей
 */

class User
{
    // Свойства

    /**
     * @var int ID пользователя
     */
    public $id = null;

    /**
     * @var string Логин пользователя
     */
    public $login = null;

    /**
     * @var string Пароль пользователя
     */
    public $password = null;

    /**
     * @var bool Статус пользователя (1 - заблокирован)
     */
    public $blocked = null;


    /**
     * Устанавливаем свойства объекта с использованием значений в передаваемом массиве
     *
     * @param assoc Значения свойств
     */

    /*public function __construct( $data=array() ) {
      if ( isset( $data['id'] ) ) $this->id = (int) $data['id'];
      if ( isset( $data['name'] ) ) $this->name = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['name'] );
      if ( isset( $data['description'] ) ) $this->description = preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['description'] );
    }*/

    public function __construct( $data=array() ) {
        if ( isset( $data['id'] ) ) $this->id = (int) $data['id'];
        if ( isset( $data['username'] ) ) $this->login = $data['username'];
        if ( isset( $data['login'] ) ) $this->login = $data['login'];
        if ( isset( $data['password'] ) ) $this->password = $data['password'];
        if ( isset( $data['blocked'] ) ) $this->blocked = $data['blocked'];
    }

    /**
     * Устанавливаем свойства объекта с использованием значений из формы редактирования
     *
     * @param assoc Значения из формы редактирования
     */

    public function storeFormValues ( $params ) {
        // Store all the parameters
        $this->__construct( $params );
    }


    /**
     * Возвращаем объект User, соответствующий заданному ID
     *
     * @param int ID Пользователя
     * @return Category|false Объект Category object или false, если запись не была найдена или в случае другой ошибки
     */

    public static function getById( $id )
    {
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        $sql = "SELECT * FROM users WHERE id = :id";
        $st = $conn->prepare( $sql );
        $st->bindValue(":id", $id, PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch();
        $conn = null;
        if ($row)
            return new User($row);
    }

    public static function getByLogin($login)
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "SELECT * from users WHERE username = :login";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $login, PDO::PARAM_STR);
        $st->execute();
        $row = $st->fetch();
        if ($row) {
            return new User($row);
        }
    }


    /**
     * Возвращаем все (или диапазон) объектов Category из базы данных
     *
     * @param int Optional Количество возвращаемых строк (по умолчаниюt = all)
     * @param string Optional Столбец, по которому сортируются категории(по умолчанию = "name ASC")
     * @return Array|false Двух элементный массив: results => массив с объектами Category; totalRows => общее количество категорий
     */
    public static function getList( $numRows=1000000, $order="id ASC" )
    {
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD);
        //	    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM categories
        //	            ORDER BY " . mysql_escape_string($order) . " LIMIT :numRows";

        //            $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM categories
        //	            ORDER BY " .$conn->query($order) . " LIMIT :numRows";

        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM users
            ORDER BY $order LIMIT :numRows";

        $st = $conn->prepare( $sql );
        $st->bindValue( ":numRows", $numRows, PDO::PARAM_INT );
        $st->execute();
        $list = array();

        while ( $row = $st->fetch() ) {
            $user = new User( $row );
            $list[] = $user;
        }

        // Получаем общее количество категорий, которые соответствуют критериям
        $sql = "SELECT FOUND_ROWS() AS totalRows";
        $totalRows = $conn->query( $sql )->fetch();
        $conn = null;
        return ( array ( "results" => $list, "totalRows" => $totalRows[0] ) );
    }


    /**
     * Вставляем текущий объект User в базу данных и устанавливаем его свойство ID.
     */

    public function insert() {

        // У объекта Category уже есть ID?
        if ( !is_null( $this->id ) ) trigger_error ( "User::insert(): Attempt to insert a User object that already has its ID property set (to $this->id).", E_USER_ERROR );

        $this->blocked = (isset($_REQUEST['blocked'])) ? 1 : 0;

        // Вставляем категорию
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        $sql = "INSERT INTO users ( login, password, blocked ) VALUES ( :login, :password, :blocked )";
        $st = $conn->prepare ( $sql );
        $st->bindValue( ":login", $this->login, PDO::PARAM_STR );
        $st->bindValue( ":password", $this->password, PDO::PARAM_STR );
        $st->bindValue( ":blocked", $this->blocked, PDO::PARAM_INT );
        $st->execute();
        $this->id = $conn->lastInsertId();
        $conn = null;
    }


    /**
     * Обновляем текущий объект Category в базе данных.
     */

    public function update() {

        // У объекта Category  есть ID?
        if ( is_null( $this->id ) ) trigger_error ( "uSER::update(): Attempt to update a uSER object that does not have its ID property set.", E_USER_ERROR );

        $this->blocked = (isset($_REQUEST['blocked'])) ? 1 : 0;
        if (strlen($_REQUEST['password']) == 0) {
            $this->password = null;
        }
        // Обновляем категорию
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        if (!is_null($this->password)) {
            $sql = "UPDATE users SET login=:login, password=:password, blocked=:blocked WHERE id = :id";
            $st = $conn->prepare ( $sql );
            $st->bindValue( ":password", $this->password, PDO::PARAM_STR );
        } else {
            $sql = "UPDATE users SET login=:login, blocked=:blocked WHERE id = :id";
            $st = $conn->prepare ( $sql );
        }
        $st->bindValue( ":login", $this->login, PDO::PARAM_STR );
        $st->bindValue( ":blocked", $this->blocked, PDO::PARAM_INT );
        $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
        $st->execute();
        $conn = null;
    }


    /**
     * Удаляем текущий объект Category из базы данных.
     */

    public function delete() {

        // У объекта Category  есть ID?
        if ( is_null( $this->id ) ) trigger_error ( "Category::delete(): Attempt to delete a Category object that does not have its ID property set.", E_USER_ERROR );

        // Удаляем категорию
        $conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
        $st = $conn->prepare ( "DELETE FROM users WHERE id = :id LIMIT 1" );
        $st->bindValue( ":id", $this->id, PDO::PARAM_INT );
        $st->execute();
        $conn = null;
    }
}