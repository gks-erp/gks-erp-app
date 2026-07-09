<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

declare(strict_types=1);


namespace gks_dav\CardDAV\Backend;

use Sabre\CardDAV;
use Sabre\DAV;
use Sabre\DAV\PropPatch;

/**
 * PDO CardDAV backend.
 *
 * This CardDAV backend uses PDO to store addressbooks
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class gks_PDO extends \Sabre\CardDAV\Backend\AbstractBackend implements \Sabre\CardDAV\Backend\SyncSupport
{
  
    public $gks_perm_condition01='';
    /**
     * PDO connection.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * The PDO table name used to store addressbooks.
     */
    public $addressBooksTableName = 'addressbooks';

    /**
     * The PDO table name used to store cards.
     */
    public $cardsTableName = 'cards';

    /**
     * The table name that will be used for tracking changes in address books.
     *
     * @var string
     */
    public $addressBookChangesTableName = 'gks_users_dav_changes';

    /**
     * Sets up the object.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Returns the list of addressbooks for a specific user.
     *
     * @param string $principalUri
     *
     * @return array
     */
    public function getAddressBooksForUser($principalUri)
    {
        $user_id=0; //0001;
        $user_name='agnostoatomo';
        $user_transparent=0;
        
        $parts=explode('/',$principalUri);
        if (count($parts)==2 and $parts[0]=='principals') {
  
          $sql="SELECT gks_settings_users.user_id, ".GKS_WP_TABLE_PREFIX."users.user_login, ".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_settings_users
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_settings_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_settings_users.myobject='dav'
          AND gks_settings_users.mysubobject='password'
          AND gks_settings_users.myvalue<>''
          AND ".GKS_WP_TABLE_PREFIX."users.user_login= ? 
          AND ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities Not Like '%subscriber%' 
          and ".GKS_WP_TABLE_PREFIX."users.ID > 0
          and ".GKS_WP_TABLE_PREFIX."users.user_login<>''
          and ".GKS_WP_TABLE_PREFIX."users.user_email <>''
          and ".GKS_WP_TABLE_PREFIX."users.gks_nickname <>''
          limit 1";
  
          $stmt = $this->pdo->prepare($sql);
          $stmt->execute([$parts[1]]);        
          while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $user_id=intval($row['user_id']);
            $user_name=trim($row['user_login']);
            break;
          }
          if ($user_id==0) die(); //not found
        } else {
          echo $principalUri; die();
          
        }
        //file_put_contents(GKS_SITE_PATH.'tmp/getAddressBooksForUser 1.txt',$user_id.' '.$user_name);
      
        $sql="select perm_view,perm_condition01 from gks_permission_user where user_id= ? and permission_object_id=115";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);        
        $perm_view=0;
        $perm_condition01='';
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $perm_view=intval($row['perm_view']);
          if (empty($row['perm_condition01'])==false) {
            $perm_condition01=' and '.trim($row['perm_condition01']);
          }
          break;
        }
        
        $this->gks_perm_condition01=$perm_condition01;
        
        
        $carddav_synctoken='1';

        //echo 'ddddddddddd';die();

        $stmt = $this->pdo->prepare("select myvalue from gks_settings where mykey='carddav_synctoken'");
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $carddav_synctoken = $row['myvalue'];
        }
        $addressBooks = [];
        
        if ($perm_view==1) {
          $addressBooks[] = [
              'id' => (string)$user_id,
              'uri' => 'default', //$row['uri'],
              'principaluri' => 'principals/'.$user_name, //$row['principaluri'],
              '{DAV:}displayname' => 'gks.gr '.$user_name.' cards', //$row['displayname'],
              '{'.CardDAV\Plugin::NS_CARDDAV.'}addressbook-description' => $user_name.' cards', //$row['description'],
              '{http://calendarserver.org/ns/}getctag' => $carddav_synctoken,
              '{http://sabredav.org/ns}sync-token' => $carddav_synctoken,
          ];  
        }
        //print '<pre>';var_dump($addressBooks);die(); 
        
        //file_put_contents(GKS_SITE_PATH.'tmp/getAddressBooksForUser 2.txt',$user_id.' '.$user_name.' '.print_r($addressBooks, true));
        return $addressBooks;
        
//        echo '<pre>';echo $carddav_synctoken;die();
//        
//
//        $stmt = $this->pdo->prepare('SELECT id, uri, displayname, principaluri, description, synctoken FROM '.$this->addressBooksTableName.' WHERE principaluri = ?');
//        $stmt->execute([$principalUri]);
//
//        $addressBooks = [];
//
//        foreach ($stmt->fetchAll() as $row) {
//            $addressBooks[] = [
//                'id' => $row['id'],
//                'uri' => $row['uri'],
//                'principaluri' => $row['principaluri'],
//                '{DAV:}displayname' => $row['displayname'],
//                '{'.CardDAV\Plugin::NS_CARDDAV.'}addressbook-description' => $row['description'],
//                '{http://calendarserver.org/ns/}getctag' => $row['synctoken'],
//                '{http://sabredav.org/ns}sync-token' => $row['synctoken'] ? $row['synctoken'] : '0',
//            ];
//        }
//
//        return $addressBooks;
    }

    /**
     * Updates properties for an address book.
     *
     * The list of mutations is stored in a Sabre\DAV\PropPatch object.
     * To do the actual updates, you must tell this object which properties
     * you're going to process with the handle() method.
     *
     * Calling the handle method is like telling the PropPatch object "I
     * promise I can handle updating this property".
     *
     * Read the PropPatch documentation for more info and examples.
     *
     * @param string $addressBookId
     */
    public function updateAddressBook($addressBookId, PropPatch $propPatch)
    {
      //file_put_contents(GKS_SITE_PATH.'tmp/888.txt','');
      echo time(); die();
      
        $supportedProperties = [
            '{DAV:}displayname',
            '{'.CardDAV\Plugin::NS_CARDDAV.'}addressbook-description',
        ];

        $propPatch->handle($supportedProperties, function ($mutations) use ($addressBookId) {
            $updates = [];
            foreach ($mutations as $property => $newValue) {
                switch ($property) {
                    case '{DAV:}displayname':
                        $updates['displayname'] = $newValue;
                        break;
                    case '{'.CardDAV\Plugin::NS_CARDDAV.'}addressbook-description':
                        $updates['description'] = $newValue;
                        break;
                }
            }
            $query = 'UPDATE '.$this->addressBooksTableName.' SET ';
            $first = true;
            foreach ($updates as $key => $value) {
                if ($first) {
                    $first = false;
                } else {
                    $query .= ', ';
                }
                $query .= ' '.$key.' = :'.$key.' ';
            }
            $query .= ' WHERE id = :addressbookid';

            $stmt = $this->pdo->prepare($query);
            $updates['addressbookid'] = $addressBookId;

            $stmt->execute($updates);

            $this->addChange($addressBookId, '', 2);

            return true;
        });
    }

    /**
     * Creates a new address book.
     *
     * @param string $principalUri
     * @param string $url          just the 'basename' of the url
     *
     * @return int Last insert id
     */
    public function createAddressBook($principalUri, $url, array $properties)
    {
      //file_put_contents(GKS_SITE_PATH.'tmp/999.txt','');
      echo time(); die();
        $values = [
            'displayname' => null,
            'description' => null,
            'principaluri' => $principalUri,
            'uri' => $url,
        ];

        foreach ($properties as $property => $newValue) {
            switch ($property) {
                case '{DAV:}displayname':
                    $values['displayname'] = $newValue;
                    break;
                case '{'.CardDAV\Plugin::NS_CARDDAV.'}addressbook-description':
                    $values['description'] = $newValue;
                    break;
                default:
                    throw new DAV\Exception\BadRequest('Unknown property: '.$property);
            }
        }

        $query = 'INSERT INTO '.$this->addressBooksTableName.' (uri, displayname, description, principaluri, synctoken) VALUES (:uri, :displayname, :description, :principaluri, 1)';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);

        return $this->pdo->lastInsertId(
            $this->addressBooksTableName.'_id_seq'
        );
    }

    /**
     * Deletes an entire addressbook and all its contents.
     *
     * @param int $addressBookId
     */
    public function deleteAddressBook($addressBookId)
    {
      //file_put_contents(GKS_SITE_PATH.'tmp/222.txt','');
      echo time(); die();
        $stmt = $this->pdo->prepare('DELETE FROM '.$this->cardsTableName.' WHERE addressbookid = ?');
        $stmt->execute([$addressBookId]);

        $stmt = $this->pdo->prepare('DELETE FROM '.$this->addressBooksTableName.' WHERE id = ?');
        $stmt->execute([$addressBookId]);

        $stmt = $this->pdo->prepare('DELETE FROM '.$this->addressBookChangesTableName.' WHERE addressbookid = ?');
        $stmt->execute([$addressBookId]);
    }

    /**
     * Returns all cards for a specific addressbook id.
     *
     * This method should return the following properties for each card:
     *   * carddata - raw vcard data
     *   * uri - Some unique url
     *   * lastmodified - A unix timestamp
     *
     * It's recommended to also return the following properties:
     *   * etag - A unique etag. This must change every time the card changes.
     *   * size - The size of the card in bytes.
     *
     * If these last two properties are provided, less time will be spent
     * calculating them. If they are specified, you can also ommit carddata.
     * This may speed up certain requests, especially with large cards.
     *
     * @param mixed $addressbookId
     *
     * @return array
     */
    public function getCards($addressbookId)
    {
        //file_put_contents(GKS_SITE_PATH.'tmp/getCards 1.txt',$addressbookId);
        
        
        $stmt = $this->pdo->prepare('SELECT ID as id, uid as uri, mydate_edit as lastmodified, etag, size 
        FROM gks_user_carddav 
        where uid is not null'. $this->gks_perm_condition01);
        $stmt->execute();

        $result = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $row['uri'] = $row['uri'].'.vcf';
            $row['etag'] = '"'.$row['etag'].'"';
            $row['lastmodified'] = (int) strtotime($row['lastmodified']);
            $result[] = $row;
        }
        //file_put_contents(GKS_SITE_PATH.'tmp/getCards 2.txt',print_r($result,true));
        return $result;
    }

    /**
     * Returns a specific card.
     *
     * The same set of properties must be returned as with getCards. The only
     * exception is that 'carddata' is absolutely required.
     *
     * If the card does not exist, you must return false.
     *
     * @param mixed  $addressBookId
     * @param string $cardUri
     *
     * @return array
     */
    public function getCard($addressBookId, $cardUri)
    {   
        //file_put_contents(GKS_SITE_PATH.'tmp/getCard '.rand(1000,9999).'.txt',$addressBookId .' '.$cardUri);
        $cardUri_cut=substr($cardUri, 0,strlen($cardUri)-4);
        $stmt = $this->pdo->prepare('SELECT ID as id, carddata, uid as uri, mydate_edit as lastmodified, etag, size 
        FROM gks_user_carddav 
        WHERE uid = ? '. $this->gks_perm_condition01. ' LIMIT 1');
        $stmt->execute([$cardUri_cut]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        $result['etag'] = '"'.$result['etag'].'"';
        $result['lastmodified'] = (int) strtotime($result['lastmodified']);

        return $result;
    }

    /**
     * Returns a list of cards.
     *
     * This method should work identical to getCard, but instead return all the
     * cards in the list as an array.
     *
     * If the backend supports this, it may allow for some speed-ups.
     *
     * @param mixed $addressBookId
     *
     * @return array
     */
    public function getMultipleCards($addressBookId, array $uris)
    {
        //file_put_contents(GKS_SITE_PATH.'tmp/getMultipleCards '.rand(1000,9999).'.txt',print_r($uris,true));
      
        $uris_cut=array();
        foreach ($uris as $value) {
          $uris_cut[]=substr($value, 0,strlen($value)-4);
        } 
        
        $query = 'SELECT ID as id, uid as uri, mydate_edit as lastmodified, etag, size, carddata 
        FROM gks_user_carddav 
        WHERE uid IN (';
        // Inserting a whole bunch of question marks
        $query .= implode(',', array_fill(0, count($uris_cut), '?'));
        $query .= ') '. $this->gks_perm_condition01;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($uris_cut);
        $result = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $row['uri'] = $row['uri'].'.vcf';
            $row['etag'] = '"'.$row['etag'].'"';
            $row['lastmodified'] = (int) strtotime($row['lastmodified']);
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Creates a new card.
     *
     * The addressbook id will be passed as the first argument. This is the
     * same id as it is returned from the getAddressBooksForUser method.
     *
     * The cardUri is a base uri, and doesn't include the full path. The
     * cardData argument is the vcard body, and is passed as a string.
     *
     * It is possible to return an ETag from this method. This ETag is for the
     * newly created resource, and must be enclosed with double quotes (that
     * is, the string itself must contain the double quotes).
     *
     * You should only return the ETag if you store the carddata as-is. If a
     * subsequent GET request on the same card does not have the same body,
     * byte-by-byte and you did return an ETag here, clients tend to get
     * confused.
     *
     * If you don't return an ETag, you can just return null.
     *
     * @param mixed  $addressBookId
     * @param string $cardUri
     * @param string $cardData
     *
     * @return string|null
     */
    public function createCard($addressBookId, $cardUri, $cardData)
    {
      //file_put_contents(GKS_SITE_PATH.'tmp/555.txt','');
      
      echo time(); die();
        $stmt = $this->pdo->prepare('INSERT INTO '.$this->cardsTableName.' (carddata, uri, lastmodified, addressbookid, size, etag) VALUES (?, ?, ?, ?, ?, ?)');

        $etag = md5($cardData);

        $stmt->execute([
            $cardData,
            $cardUri,
            time(),
            $addressBookId,
            strlen($cardData),
            $etag,
        ]);

        $this->addChange($addressBookId, $cardUri, 1);

        return '"'.$etag.'"';
    }

    /**
     * Updates a card.
     *
     * The addressbook id will be passed as the first argument. This is the
     * same id as it is returned from the getAddressBooksForUser method.
     *
     * The cardUri is a base uri, and doesn't include the full path. The
     * cardData argument is the vcard body, and is passed as a string.
     *
     * It is possible to return an ETag from this method. This ETag should
     * match that of the updated resource, and must be enclosed with double
     * quotes (that is: the string itself must contain the actual quotes).
     *
     * You should only return the ETag if you store the carddata as-is. If a
     * subsequent GET request on the same card does not have the same body,
     * byte-by-byte and you did return an ETag here, clients tend to get
     * confused.
     *
     * If you don't return an ETag, you can just return null.
     *
     * @param mixed  $addressBookId
     * @param string $cardUri
     * @param string $cardData
     *
     * @return string|null
     */
    public function updateCard($addressBookId, $cardUri, $cardData)
    {
      //file_put_contents(GKS_SITE_PATH.'tmp/666.txt','');
      echo time(); die();
      
        $stmt = $this->pdo->prepare('UPDATE '.$this->cardsTableName.' SET carddata = ?, lastmodified = ?, size = ?, etag = ? WHERE uri = ? AND addressbookid =?');

        $etag = md5($cardData);
        $stmt->execute([
            $cardData,
            time(),
            strlen($cardData),
            $etag,
            $cardUri,
            $addressBookId,
        ]);

        $this->addChange($addressBookId, $cardUri, 2);

        return '"'.$etag.'"';
    }

    /**
     * Deletes a card.
     *
     * @param mixed  $addressBookId
     * @param string $cardUri
     *
     * @return bool
     */
    public function deleteCard($addressBookId, $cardUri)
    {
      //file_put_contents(GKS_SITE_PATH.'tmp/777.txt','');
      echo time();die();
      
        $stmt = $this->pdo->prepare('DELETE FROM '.$this->cardsTableName.' WHERE addressbookid = ? AND uri = ?');
        $stmt->execute([$addressBookId, $cardUri]);

        $this->addChange($addressBookId, $cardUri, 3);

        return 1 === $stmt->rowCount();
    }

    /**
     * The getChanges method returns all the changes that have happened, since
     * the specified syncToken in the specified address book.
     *
     * This function should return an array, such as the following:
     *
     * [
     *   'syncToken' => 'The current synctoken',
     *   'added'   => [
     *      'new.txt',
     *   ],
     *   'modified'   => [
     *      'updated.txt',
     *   ],
     *   'deleted' => [
     *      'foo.php.bak',
     *      'old.txt'
     *   ]
     * ];
     *
     * The returned syncToken property should reflect the *current* syncToken
     * of the addressbook, as reported in the {http://sabredav.org/ns}sync-token
     * property. This is needed here too, to ensure the operation is atomic.
     *
     * If the $syncToken argument is specified as null, this is an initial
     * sync, and all members should be reported.
     *
     * The modified property is an array of nodenames that have changed since
     * the last token.
     *
     * The deleted property is an array with nodenames, that have been deleted
     * from collection.
     *
     * The $syncLevel argument is basically the 'depth' of the report. If it's
     * 1, you only have to report changes that happened only directly in
     * immediate descendants. If it's 2, it should also include changes from
     * the nodes below the child collections. (grandchildren)
     *
     * The $limit argument allows a client to specify how many results should
     * be returned at most. If the limit is not specified, it should be treated
     * as infinite.
     *
     * If the limit (infinite or not) is higher than you're willing to return,
     * you should throw a Sabre\DAV\Exception\TooMuchMatches() exception.
     *
     * If the syncToken is expired (due to data cleanup) or unknown, you must
     * return null.
     *
     * The limit is 'suggestive'. You are free to ignore it.
     *
     * @param string $addressBookId
     * @param string $syncToken
     * @param int    $syncLevel
     * @param int    $limit
     *
     * @return array
     */
    public function getChangesForAddressBook($addressBookId, $syncToken, $syncLevel, $limit = null)
    {
        //die();
        //file_put_contents(GKS_SITE_PATH.'tmp/111.txt',$addressBookId.'|'.$syncToken.'|'.$syncLevel.'|');
        //file_put_contents(GKS_SITE_PATH.'tmp/getChangesForAddressBook 1.txt','');

        // Current synctoken
        $stmt = $this->pdo->prepare("select myvalue from gks_settings where mykey='carddav_synctoken'");
        $stmt->execute();
        $currentToken = $stmt->fetchColumn(0);

        if (is_null($currentToken)) {
            return null;
        }

        $result = [
            'syncToken' => $currentToken,
            'added' => [],
            'modified' => [],
            'deleted' => [],
        ];

        if ($syncToken) {
            $query = 'SELECT uri, operation FROM '.$this->addressBookChangesTableName.' WHERE synctoken >= ? AND synctoken < ? ORDER BY synctoken';
            if ($limit > 0) {
                $query .= ' LIMIT '.(int) $limit;
            }

            // Fetching all changes
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$syncToken, $currentToken]);

            $changes = [];

            // This loop ensures that any duplicates are overwritten, only the
            // last change on a node is relevant.
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $changes[$row['uri']] = $row['operation'];
            }

            foreach ($changes as $uri => $operation) {
                switch ($operation) {
                    case 1:
                        $result['added'][] = $uri;
                        break;
                    case 2:
                        $result['modified'][] = $uri;
                        break;
                    case 3:
                        $result['deleted'][] = $uri;
                        break;
                }
            }
        } else {
            // No synctoken supplied, this is the initial sync.
            $query = 'SELECT CONCAT(uid, ".vcf") as uri 
            FROM gks_user_carddav 
            where uid is not null '. $this->gks_perm_condition01;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();

            $result['added'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }
        
        //file_put_contents(GKS_SITE_PATH.'tmp/111-1.txt',print_r($result,true));
        
        return $result;
    }

    /**
     * Adds a change record to the addressbookchanges table.
     *
     * @param mixed  $addressBookId
     * @param string $objectUri
     * @param int    $operation     1 = add, 2 = modify, 3 = delete
     */
    protected function addChange($addressBookId, $objectUri, $operation)
    {
      //file_put_contents(GKS_SITE_PATH.'tmp/333.txt','');
      echo time(); die();
        $stmt = $this->pdo->prepare('INSERT INTO '.$this->addressBookChangesTableName.' (uri, synctoken, addressbookid, operation) SELECT ?, synctoken, ?, ? FROM '.$this->addressBooksTableName.' WHERE id = ?');
        $stmt->execute([
            $objectUri,
            $addressBookId,
            $operation,
            $addressBookId,
        ]);
        $stmt = $this->pdo->prepare('UPDATE '.$this->addressBooksTableName.' SET synctoken = synctoken + 1 WHERE id = ?');
        $stmt->execute([
            $addressBookId,
        ]);
    }
}
