<?php
/**
 * (c) Albireo Framework, https://maxsite.org/albireo, 2021
 *
 * PdoConnect
 * Return PDO connect (or false)
 * 
 * // config base located in the file
 * if ($configDB = getConfigFile(CONFIG_DIR . 'dbase.php', 'mybase')) {
 *		$pdo = Pdo\PdoConnect::getInstance();
 *		$db = $pdo->connect($configDB);
 * } 
 *		
 * if (empty($db)) return;
 * 
 * Pdo\PdoQuery::execute($db, $sql, $vars) ...
 * 
 * Samples config:
 * # Sqlite
 * [
 *	    'dsn' => 'sqlite:' . DATA_DIR . 'storage' . DIRECTORY_SEPARATOR . 'my.sqlite',
 * ]
 * 
 * # MySQL
 * [
 *		'dsn' => 'mysql:host=127.0.0.1;dbname=MYDB;charset:UTF8',
 *		'username' => 'user',
 *		'password' => '123456',
 *		'options' => [
 *			\PDO::ATTR_PERSISTENT => true,
 *          \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
 *		],
 *      'queries' => [
 *			'SET NAMES UTF8',
 *		],
 *	]
 * 
 */

namespace Pdo;

class PdoConnect
{
    use \Pattern\Singleton;

    private $connectedDB = []; // All alias connections

    /**
     * Connect to base
     * 
     * @param array $configDB
     * @return PDO
     */
    public function connect(array $configDB = [])
    {
        $alias = crc32(serialize($configDB));

        if (isset($this->connectedDB[$alias]))
            return $this->connectedDB[$alias];

        return $this->connectDSN($alias, $configDB);
    }

    /**
     * Connect function
     * @param string $alias
     * @param array $configDB
     * @return PDO or false
     */
    private function connectDSN(string $alias, array $configDB)
    {
        if (empty($configDB['dsn'])) {
            $this->showMessage('Need to set DSN in config database');
            return false;
        }

        $username = $configDB['username'] ?? '';
        $password = $configDB['password'] ?? '';
        $queries = $configDB['queries'] ?? false;
        $options = $configDB['options'] ?? [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC];

        try {
            $db = new \PDO($configDB['dsn'], $username, $password, $options);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->connectedDB[$alias] = $db;

            if ($queries) {
                foreach ($queries as $sql) {
                    try {
                        $db->query($sql);
                    } catch (\PDOException $e) {
                        $this->showMessage('Error: ' . $e->getMessage());
                    }
                }
            }
        } catch (\PDOException $e) {
            $this->showMessage('Connection to database failed: ' . $e->getMessage());

            return false;
        }

        return $this->connectedDB[$alias];
    }

    /**
     * Show message
     * 
     * @param string $message
     * @return void
     */
    private function showMessage(string $message)
    {
        echo '<div class="pad10 bg-red700 t90 t-white t-center"><i class="im-exclamation-triangle"></i>' . $message . '</div>';
    }
}

# end of file
