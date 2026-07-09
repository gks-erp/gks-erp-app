<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


declare(strict_types=1);

namespace gks_dav\Auth\Backend;

/**
 * This is an authentication backend that uses a database to manage passwords.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class gks_PDO extends \Sabre\DAV\Auth\Backend\AbstractDigest
{
    /**
     * Reference to PDO connection.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * PDO table name we'll be using.
     *
     * @var string
     */
    public $tableName = 'users';

    /**
     * Creates the backend object.
     *
     * If the filename argument is passed in, it will parse out the specified file fist.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Returns the digest hash for a user.
     *
     * @param string $realm
     * @param string $username
     *
     * @return string|null
     */
    public function getDigestHash($realm, $username)
    {
        $user_login='agnostoatomo';
        $stmt = $this->pdo->prepare('SELECT ID,user_login FROM '.GKS_WP_TABLE_PREFIX.'users WHERE user_login = ? limit 1');
        $stmt->execute([$username]);
        $user_id=0;
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $user_id=$row['ID'];
          $user_login=trim($row['user_login']);
        }
        //echo $user_id; die();
        //gks_users_defaults
        //gks_settings_users
        
        $stmt = $this->pdo->prepare('SELECT myvalue FROM gks_settings_users WHERE user_id= ? and myobject = ? and mysubobject = ?');
        $stmt->execute([$user_id,'dav','password']);
        
        $user_pass=null;$user_pass_plain='';
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          if (trim($row['myvalue']) != '') {
            $user_pass=md5($user_login.':'.$realm.':'.trim($row['myvalue'])); //trim($row['myvalue']);
            $user_pass_plain=$row['myvalue'];
          }
        }
        //echo $user_pass; die();
        //echo md5('kostas1:SabreDAV:kostas2');die();
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getDigestHash.txt',$realm.'|'.$username.'|'.$user_pass.'|'.$user_pass_plain);
        //echo $username.'|'.$user_login.'|'.$user_pass; die();
        
        //echo $realm.'|'.$username.'|'.time();die();

        return $user_pass;
    }
}
