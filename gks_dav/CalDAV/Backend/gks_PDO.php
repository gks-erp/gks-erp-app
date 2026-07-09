<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

declare(strict_types=1);

namespace gks_dav\CalDAV\Backend;




use Sabre\CalDAV;
use Sabre\DAV;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAV\PropPatch;
use Sabre\DAV\Xml\Element\Sharee;
use Sabre\VObject;

//use Sabre\CalDAV\Backend;


/**
 * PDO CalDAV backend.
 *
 * This backend is used to store calendar-data in a PDO database, such as
 * sqlite or MySQL
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class gks_PDO extends \Sabre\CalDAV\Backend\AbstractBackend implements \Sabre\CalDAV\Backend\SyncSupport, \Sabre\CalDAV\Backend\SubscriptionSupport, \Sabre\CalDAV\Backend\SchedulingSupport, \Sabre\CalDAV\Backend\SharingSupport
{
    /**
     * We need to specify a max date, because we need to stop *somewhere*.
     *
     * On 32 bit system the maximum for a signed integer is 2147483647, so
     * MAX_DATE cannot be higher than date('Y-m-d', 2147483647) which results
     * in 2038-01-19 to avoid problems when the date is converted
     * to a unix timestamp.
     */
    const MAX_DATE = '2038-01-01';

    /**
     * pdo.
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * The table name that will be used for calendars.
     *
     * @var string
     */
    //public $calendarTableName = 'calendars';

    /**
     * The table name that will be used for calendars instances.
     *
     * A single calendar can have multiple instances, if the calendar is
     * shared.
     *
     * @var string
     */
    public $calendarInstancesTableName = 'calendarinstances';

    /**
     * The table name that will be used for calendar objects.
     *
     * @var string
     */
    public $calendarObjectTableName = 'gks_calendar'; //'calendarobjects';

    /**
     * The table name that will be used for tracking changes in calendars.
     *
     * @var string
     */
    //public $calendarChangesTableName = 'gks_calendar_dav_changes'; //'calendarchanges';

    /**
     * The table name that will be used inbox items.
     *
     * @var string
     */
    public $schedulingObjectTableName = 'schedulingobjects';

    /**
     * The table name that will be used for calendar subscriptions.
     *
     * @var string
     */
    public $calendarSubscriptionsTableName = 'calendarsubscriptions';

    /**
     * List of CalDAV properties, and how they map to database fieldnames
     * Add your own properties by simply adding on to this array.
     *
     * Note that only string-based properties are supported here.
     *
     * @var array
     */
    public $propertyMap = [
        '{DAV:}displayname' => 'displayname',
        '{urn:ietf:params:xml:ns:caldav}calendar-description' => 'description',
        '{urn:ietf:params:xml:ns:caldav}calendar-timezone' => 'timezone',
        '{http://apple.com/ns/ical/}calendar-order' => 'calendarorder',
        '{http://apple.com/ns/ical/}calendar-color' => 'calendarcolor',
    ];

    /**
     * List of subscription properties, and how they map to database fieldnames.
     *
     * @var array
     */
    public $subscriptionPropertyMap = [
        '{DAV:}displayname' => 'displayname',
        '{http://apple.com/ns/ical/}refreshrate' => 'refreshrate',
        '{http://apple.com/ns/ical/}calendar-order' => 'calendarorder',
        '{http://apple.com/ns/ical/}calendar-color' => 'calendarcolor',
        '{http://calendarserver.org/ns/}subscribed-strip-todos' => 'striptodos',
        '{http://calendarserver.org/ns/}subscribed-strip-alarms' => 'stripalarms',
        '{http://calendarserver.org/ns/}subscribed-strip-attachments' => 'stripattachments',
    ];

    /**
     * Creates the backend.
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Returns a list of calendars for a principal.
     *
     * Every project is an array with the following keys:
     *  * id, a unique id that will be used by other functions to modify the
     *    calendar. This can be the same as the uri or a database key.
     *  * uri. This is just the 'base uri' or 'filename' of the calendar.
     *  * principaluri. The owner of the calendar. Almost always the same as
     *    principalUri passed to this method.
     *
     * Furthermore it can contain webdav properties in clark notation. A very
     * common one is '{DAV:}displayname'.
     *
     * Many clients also require:
     * {urn:ietf:params:xml:ns:caldav}supported-calendar-component-set
     * For this property, you can just return an instance of
     * Sabre\CalDAV\Xml\Property\SupportedCalendarComponentSet.
     *
     * If you return {http://sabredav.org/ns}read-only and set the value to 1,
     * ACL will automatically be put in read-only mode.
     *
     * @param string $principalUri
     *
     * @return array
     */
    public function getCalendarsForUser($principalUri)
    {
        $user_id=0; //0001;
        $user_name='agnostoatomo';
        $user_transparent=0;
        
        //mail('kostas@gks.gr', 'getCalendarsForUser', $principalUri.'');
        
        
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
              
        $sql="select perm_view from gks_permission_user where user_id= ? and permission_object_id=425";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);        
        $perm_view=0;
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $perm_view=intval($row['perm_view']);
          break;
        }
        
        $caldav_synctoken='-1';
        $stmt = $this->pdo->prepare("SELECT caldav_synctoken FROM gks_calendar_dav_calendars where other_myobj='cal' and user_id=".$user_id);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
        }
        if ($caldav_synctoken == -1) { //calendar not exist
          $stmt = $this->pdo->prepare("insert into gks_calendar_dav_calendars (user_id,other_myobj,caldav_synctoken) values (".$user_id.",'cal',1)");
          $stmt->execute(); 
          $caldav_synctoken='1';
        }
        $stmt = $this->pdo->prepare("SELECT id_dav_calendar FROM gks_calendar_dav_calendars where other_myobj='cal' and user_id=".$user_id);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $id_dav_calendar = $row['id_dav_calendar'];
        }
        $stmt = $this->pdo->prepare("SELECT myvalue FROM gks_settings_users where user_id=".$user_id." and myobject='calendar' and mysubobject='user_color' and myvalue<>''");
        $stmt->execute();
        $color_calendar=null;
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $color_calendar=trim($row['myvalue']);
        } 
        
        
        
        $calendars = [];
        $components = explode(',', 'VEVENT,VTODO');
        
        $calendar = [
            'id' => [(int) $id_dav_calendar, (int) $id_dav_calendar],
            'uri' => (string)'default', //$user_name.'_cal', //
            'principaluri' => 'principals/'.$user_name, //$row['principaluri'],
            '{'.CalDAV\Plugin::NS_CALENDARSERVER.'}getctag' => 'http://sabre.io/ns/sync/'.$caldav_synctoken,
            '{http://sabredav.org/ns}sync-token' => $caldav_synctoken,
            '{'.CalDAV\Plugin::NS_CALDAV.'}supported-calendar-component-set' => new CalDAV\Xml\Property\SupportedCalendarComponentSet($components),
            '{'.CalDAV\Plugin::NS_CALDAV.'}schedule-calendar-transp' => new CalDAV\Xml\Property\ScheduleCalendarTransp($user_transparent ? 'transparent' : 'opaque'),
            'share-resource-uri' => '/ns/share/'.$id_dav_calendar,
        ];
        $calendar['share-access'] = 1; // 1 = owner, 2 = readonly, 3 = readwrite        
        
        $calendar['{DAV:}displayname']='Ημερολόγιο '.$user_name;

        $calendar['{urn:ietf:params:xml:ns:caldav}calendar-description'] = 'gks ERP Calendar';
        $calendar['{urn:ietf:params:xml:ns:caldav}calendar-timezone'] = null;
        $calendar['{http://apple.com/ns/ical/}calendar-order'] = 1;
        $calendar['{http://apple.com/ns/ical/}calendar-color'] = $color_calendar;
        
        //foreach ($this->propertyMap as $xmlName => $dbName) {
        //    $calendar[$xmlName] = $row[$dbName];
        //}
        
        if ($perm_view==1) {
          $calendars[] = $calendar; 
        }
        
        // task /////////////////////////////////////////////////
        
        
        $caldav_synctoken='-1';
        $stmt = $this->pdo->prepare("SELECT caldav_synctoken FROM gks_calendar_dav_calendars where other_myobj='task' and user_id=".$user_id);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
        }
        if ($caldav_synctoken == -1) { //calendar not exist
          $stmt = $this->pdo->prepare("insert into gks_calendar_dav_calendars (user_id,other_myobj,caldav_synctoken) values (".$user_id.",'task',1)");
          $stmt->execute(); 
          $caldav_synctoken='1';
        }
        $stmt = $this->pdo->prepare("SELECT id_dav_calendar FROM gks_calendar_dav_calendars where other_myobj='task' and user_id=".$user_id);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $id_dav_calendar = $row['id_dav_calendar'];
        }
        
        $stmt = $this->pdo->prepare("SELECT myvalue FROM gks_settings_users where user_id=".$user_id." and myobject='calendar' and mysubobject='user_color_task' and myvalue<>''");
        $stmt->execute();
        $color_calendar=null;
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $color_calendar=trim($row['myvalue']);
        }

        
        $calendar_task = [
            'id' => [(int) $id_dav_calendar, (int) $id_dav_calendar],
            'uri' => (string)'tasks', //$user_name.'_cal', //
            'principaluri' => 'principals/'.$user_name, //$row['principaluri'],
            '{'.CalDAV\Plugin::NS_CALENDARSERVER.'}getctag' => 'http://sabre.io/ns/sync/'.$caldav_synctoken,
            '{http://sabredav.org/ns}sync-token' => $caldav_synctoken,
            '{'.CalDAV\Plugin::NS_CALDAV.'}supported-calendar-component-set' => new CalDAV\Xml\Property\SupportedCalendarComponentSet($components),
            '{'.CalDAV\Plugin::NS_CALDAV.'}schedule-calendar-transp' => new CalDAV\Xml\Property\ScheduleCalendarTransp($user_transparent ? 'transparent' : 'opaque'),
            'share-resource-uri' => '/ns/share/'.$id_dav_calendar,
        ];
        $calendar_task['share-access'] = 1; // 1 = owner, 2 = readonly, 3 = readwrite        
        
        $calendar_task['{DAV:}displayname']='Εργασίες '.$user_name;

        $calendar_task['{urn:ietf:params:xml:ns:caldav}calendar-description'] = 'gks ERP Tasks';
        $calendar_task['{urn:ietf:params:xml:ns:caldav}calendar-timezone'] = null;
        $calendar_task['{http://apple.com/ns/ical/}calendar-order'] = 2;
        $calendar_task['{http://apple.com/ns/ical/}calendar-color'] = $color_calendar;
        
        if ($perm_view==1) {
          $calendars[] = $calendar_task;        
        }
        //print '<pre>';var_dump($calendars);die();
        
        
        
        
        // activity /////////////////////////////////////////////////
        
        $caldav_synctoken='-1';
        $stmt = $this->pdo->prepare("SELECT caldav_synctoken FROM gks_calendar_dav_calendars where other_myobj='activity' and user_id=".$user_id);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
        }
        if ($caldav_synctoken == -1) { //calendar not exist
          $stmt = $this->pdo->prepare("insert into gks_calendar_dav_calendars (user_id,other_myobj,caldav_synctoken) values (".$user_id.",'activity',1)");
          $stmt->execute(); 
          $caldav_synctoken='1';
        }
        $stmt = $this->pdo->prepare("SELECT id_dav_calendar FROM gks_calendar_dav_calendars where other_myobj='activity' and user_id=".$user_id);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $id_dav_calendar = $row['id_dav_calendar'];
        }
        
        $stmt = $this->pdo->prepare("SELECT myvalue FROM gks_settings_users where user_id=".$user_id." and myobject='calendar' and mysubobject='user_color_activ' and myvalue<>''");
        $stmt->execute();
        $color_calendar=null;
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $color_calendar=trim($row['myvalue']);
        }

        
        $calendar_activity = [
            'id' => [(int) $id_dav_calendar, (int) $id_dav_calendar],
            'uri' => (string)'activity', //$user_name.'_cal', //
            'principaluri' => 'principals/'.$user_name, //$row['principaluri'],
            '{'.CalDAV\Plugin::NS_CALENDARSERVER.'}getctag' => 'http://sabre.io/ns/sync/'.$caldav_synctoken,
            '{http://sabredav.org/ns}sync-token' => $caldav_synctoken,
            '{'.CalDAV\Plugin::NS_CALDAV.'}supported-calendar-component-set' => new CalDAV\Xml\Property\SupportedCalendarComponentSet($components),
            '{'.CalDAV\Plugin::NS_CALDAV.'}schedule-calendar-transp' => new CalDAV\Xml\Property\ScheduleCalendarTransp($user_transparent ? 'transparent' : 'opaque'),
            'share-resource-uri' => '/ns/share/'.$id_dav_calendar,
        ];
        $calendar_activity['share-access'] = 1; // 1 = owner, 2 = readonly, 3 = readwrite        
        
        $calendar_activity['{DAV:}displayname']='Δραστηριότητα '.$user_name;

        $calendar_activity['{urn:ietf:params:xml:ns:caldav}calendar-description'] = 'gks ERP Activity';
        $calendar_activity['{urn:ietf:params:xml:ns:caldav}calendar-timezone'] = null;
        $calendar_activity['{http://apple.com/ns/ical/}calendar-order'] = 4;
        $calendar_activity['{http://apple.com/ns/ical/}calendar-color'] = $color_calendar;
        
        if ($perm_view==1) {
          $calendars[] = $calendar_activity;        
        }
        //print '<pre>';var_dump($calendars);die();        
        
        
        
        // transfer_reservation /////////////////////////////////////////////////
        
        
        $caldav_synctoken='-1';
        $stmt = $this->pdo->prepare("SELECT caldav_synctoken FROM gks_calendar_dav_calendars where other_myobj='transfer_reservation' and user_id=".$user_id);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
        }
        if ($caldav_synctoken == -1) { //calendar not exist
          $stmt = $this->pdo->prepare("insert into gks_calendar_dav_calendars (user_id,other_myobj,caldav_synctoken) values (".$user_id.",'transfer_reservation',1)");
          $stmt->execute(); 
          $caldav_synctoken='1';
        }
        $stmt = $this->pdo->prepare("SELECT id_dav_calendar FROM gks_calendar_dav_calendars where other_myobj='transfer_reservation' and user_id=".$user_id);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $id_dav_calendar = $row['id_dav_calendar'];
        }
        
        $stmt = $this->pdo->prepare("SELECT myvalue FROM gks_settings_users where user_id=".$user_id." and myobject='gks_transfer_reservation' and mysubobject='user_color_transfer' and myvalue<>''");
        $stmt->execute();
        $color_calendar=null;
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $color_calendar=trim($row['myvalue']);
        }

        
        $calendar_transfers = [
            'id' => [(int) $id_dav_calendar, (int) $id_dav_calendar],
            'uri' => (string)'transfers', //$user_name.'_cal', //
            'principaluri' => 'principals/'.$user_name, //$row['principaluri'],
            '{'.CalDAV\Plugin::NS_CALENDARSERVER.'}getctag' => 'http://sabre.io/ns/sync/'.$caldav_synctoken,
            '{http://sabredav.org/ns}sync-token' => $caldav_synctoken,
            '{'.CalDAV\Plugin::NS_CALDAV.'}supported-calendar-component-set' => new CalDAV\Xml\Property\SupportedCalendarComponentSet($components),
            '{'.CalDAV\Plugin::NS_CALDAV.'}schedule-calendar-transp' => new CalDAV\Xml\Property\ScheduleCalendarTransp($user_transparent ? 'transparent' : 'opaque'),
            'share-resource-uri' => '/ns/share/'.$id_dav_calendar,
        ];
        $calendar_transfers['share-access'] = 1; // 1 = owner, 2 = readonly, 3 = readwrite        
        
        $calendar_transfers['{DAV:}displayname']='Transfers '.$user_name;

        $calendar_transfers['{urn:ietf:params:xml:ns:caldav}calendar-description'] = 'gks ERP Transfers';
        $calendar_transfers['{urn:ietf:params:xml:ns:caldav}calendar-timezone'] = null;
        $calendar_transfers['{http://apple.com/ns/ical/}calendar-order'] = 3;
        $calendar_transfers['{http://apple.com/ns/ical/}calendar-color'] = $color_calendar;
        
        if ($perm_view==1) {
          $calendars[] = $calendar_transfers;        
        }
        //print '<pre>';var_dump($calendars);die();
        
        
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarsForUser.txt',print_r($calendars,true));
        return $calendars;
//        
      
//        $fields = array_values($this->propertyMap);
//        $fields[] = 'calendarid';
//        $fields[] = 'uri';
//        $fields[] = 'synctoken';
//        $fields[] = 'components';
//        $fields[] = 'principaluri';
//        $fields[] = 'transparent';
//        $fields[] = 'access';
//
//        // Making fields a comma-delimited list
//        $fields = implode(', ', $fields);
//        $stmt = $this->pdo->prepare(<<<SQL
//SELECT {$this->calendarInstancesTableName}.id as id, $fields FROM {$this->calendarInstancesTableName}
//    LEFT JOIN {$this->calendarTableName} ON
//        {$this->calendarInstancesTableName}.calendarid = {$this->calendarTableName}.id
//WHERE principaluri = ? ORDER BY calendarorder ASC
//SQL
//        );
//        
//        //echo '<pre>';
//        /*
//        SELECT calendarinstances.id as id, displayname, description, timezone, calendarorder, calendarcolor, calendarid, uri, synctoken, components, principaluri, transparent, access FROM calendarinstances
//    LEFT JOIN calendars ON
//        calendarinstances.calendarid = calendars.id
//WHERE principaluri = ? ORDER BY calendarorder ASC
//*/
//        
//        //echo $principalUri."\n"; //principals/kostas1
//        //echo $principalUri."\n"; //principals/kostas1
//        //die();
//        
//        $stmt->execute([$principalUri]);
//
//        $calendars = [];
//        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
//            $components = [];
//            if ($row['components']) {
//                $components = explode(',', $row['components']);
//            }
//
//            $calendar = [
//                'id' => [(int) $row['calendarid'], (int) $row['id']],
//                'uri' => $row['uri'],
//                'principaluri' => $row['principaluri'],
//                '{'.CalDAV\Plugin::NS_CALENDARSERVER.'}getctag' => 'http://sabre.io/ns/sync/'.($row['synctoken'] ? $row['synctoken'] : '0'),
//                '{http://sabredav.org/ns}sync-token' => $row['synctoken'] ? $row['synctoken'] : '0',
//                '{'.CalDAV\Plugin::NS_CALDAV.'}supported-calendar-component-set' => new CalDAV\Xml\Property\SupportedCalendarComponentSet($components),
//                '{'.CalDAV\Plugin::NS_CALDAV.'}schedule-calendar-transp' => new CalDAV\Xml\Property\ScheduleCalendarTransp($row['transparent'] ? 'transparent' : 'opaque'),
//                'share-resource-uri' => '/ns/share/'.$row['calendarid'],
//            ];
//
//            $calendar['share-access'] = (int) $row['access'];
//            // 1 = owner, 2 = readonly, 3 = readwrite
//            if ($row['access'] > 1) {
//                // We need to find more information about the original owner.
//                //$stmt2 = $this->pdo->prepare('SELECT principaluri FROM ' . $this->calendarInstancesTableName . ' WHERE access = 1 AND id = ?');
//                //$stmt2->execute([$row['id']]);
//
//                // read-only is for backwards compatbility. Might go away in
//                // the future.
//                $calendar['read-only'] = \Sabre\DAV\Sharing\Plugin::ACCESS_READ === (int) $row['access'];
//            }
//
//            foreach ($this->propertyMap as $xmlName => $dbName) {
//                $calendar[$xmlName] = $row[$dbName];
//            }
//
//            $calendars[] = $calendar;
//        }
//
//        print '<pre>';var_dump($calendars);die();
//        
//        return $calendars;
    }

    /**
     * Creates a new calendar for a principal.
     *
     * If the creation was a success, an id must be returned that can be used
     * to reference this calendar in other methods, such as updateCalendar.
     *
     * @param string $principalUri
     * @param string $calendarUri
     *
     * @return string
     */
    public function createCalendar($principalUri, $calendarUri, array $properties)
    {
        throw new \InvalidArgumentException('Create Calendar not supported');

        return false;
      
//        $fieldNames = [
//            'principaluri',
//            'uri',
//            'transparent',
//            'calendarid',
//        ];
//        $values = [
//            ':principaluri' => $principalUri,
//            ':uri' => $calendarUri,
//            ':transparent' => 0,
//        ];
//
//        $sccs = '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set';
//        if (!isset($properties[$sccs])) {
//            // Default value
//            $components = 'VEVENT,VTODO';
//        } else {
//            if (!($properties[$sccs] instanceof CalDAV\Xml\Property\SupportedCalendarComponentSet)) {
//                throw new DAV\Exception('The '.$sccs.' property must be of type: \Sabre\CalDAV\Xml\Property\SupportedCalendarComponentSet');
//            }
//            $components = implode(',', $properties[$sccs]->getValue());
//        }
//        $transp = '{'.CalDAV\Plugin::NS_CALDAV.'}schedule-calendar-transp';
//        if (isset($properties[$transp])) {
//            $values[':transparent'] = 'transparent' === $properties[$transp]->getValue() ? 1 : 0;
//        }
//        $stmt = $this->pdo->prepare('INSERT INTO '.$this->calendarTableName.' (synctoken, components) VALUES (1, ?)');
//        $stmt->execute([$components]);
//
//        $calendarId = $this->pdo->lastInsertId(
//            $this->calendarTableName.'_id_seq'
//        );
//
//        $values[':calendarid'] = $calendarId;
//
//        foreach ($this->propertyMap as $xmlName => $dbName) {
//            if (isset($properties[$xmlName])) {
//                $values[':'.$dbName] = $properties[$xmlName];
//                $fieldNames[] = $dbName;
//            }
//        }
//
//        $stmt = $this->pdo->prepare('INSERT INTO '.$this->calendarInstancesTableName.' ('.implode(', ', $fieldNames).') VALUES ('.implode(', ', array_keys($values)).')');
//
//        $stmt->execute($values);
//
//        return [
//            $calendarId,
//            $this->pdo->lastInsertId($this->calendarInstancesTableName.'_id_seq'),
//        ];
    }

    /**
     * Updates properties for a calendar.
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
     * @param mixed $calendarId
     */
    public function updateCalendar($calendarId, PropPatch $propPatch)
    {
        throw new \InvalidArgumentException('Update Calendar not supported');

        return false;

//        if (!is_array($calendarId)) {
//            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
//        }
//        list($calendarId, $instanceId) = $calendarId;
//
//        $supportedProperties = array_keys($this->propertyMap);
//        $supportedProperties[] = '{'.CalDAV\Plugin::NS_CALDAV.'}schedule-calendar-transp';
//
//        $propPatch->handle($supportedProperties, function ($mutations) use ($calendarId, $instanceId) {
//            $newValues = [];
//            foreach ($mutations as $propertyName => $propertyValue) {
//                switch ($propertyName) {
//                    case '{'.CalDAV\Plugin::NS_CALDAV.'}schedule-calendar-transp':
//                        $fieldName = 'transparent';
//                        $newValues[$fieldName] = 'transparent' === $propertyValue->getValue();
//                        break;
//                    default:
//                        $fieldName = $this->propertyMap[$propertyName];
//                        $newValues[$fieldName] = $propertyValue;
//                        break;
//                }
//            }
//            $valuesSql = [];
//            foreach ($newValues as $fieldName => $value) {
//                $valuesSql[] = $fieldName.' = ?';
//            }
//
//            $stmt = $this->pdo->prepare('UPDATE '.$this->calendarInstancesTableName.' SET '.implode(', ', $valuesSql).' WHERE id = ?');
//            $newValues['id'] = $instanceId;
//            $stmt->execute(array_values($newValues));
//
//            $this->addChange($calendarId, '', 2);
//
//            return true;
//        });
    }

    /**
     * Delete a calendar and all it's objects.
     *
     * @param mixed $calendarId
     */
    public function deleteCalendar($calendarId)
    {
        throw new \InvalidArgumentException('Delete Calendar not supported');

        return false;

//
//        if (!is_array($calendarId)) {
//            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
//        }
//        list($calendarId, $instanceId) = $calendarId;
//
//        $stmt = $this->pdo->prepare('SELECT access FROM '.$this->calendarInstancesTableName.' where id = ?');
//        $stmt->execute([$instanceId]);
//        $access = (int) $stmt->fetchColumn();
//
//        if (\Sabre\DAV\Sharing\Plugin::ACCESS_SHAREDOWNER === $access) {
//            /**
//             * If the user is the owner of the calendar, we delete all data and all
//             * instances.
//             **/
//            $stmt = $this->pdo->prepare('DELETE FROM '.$this->calendarObjectTableName.' WHERE calendarid = ?');
//            $stmt->execute([$calendarId]);
//
//            $stmt = $this->pdo->prepare('DELETE FROM '.$this->calendarChangesTableName.' WHERE calendarid = ?');
//            $stmt->execute([$calendarId]);
//
//            $stmt = $this->pdo->prepare('DELETE FROM '.$this->calendarInstancesTableName.' WHERE calendarid = ?');
//            $stmt->execute([$calendarId]);
//
//            $stmt = $this->pdo->prepare('DELETE FROM '.$this->calendarTableName.' WHERE id = ?');
//            $stmt->execute([$calendarId]);
//        } else {
//            /**
//             * If it was an instance of a shared calendar, we only delete that
//             * instance.
//             */
//            $stmt = $this->pdo->prepare('DELETE FROM '.$this->calendarInstancesTableName.' WHERE id = ?');
//            $stmt->execute([$instanceId]);
//        }
    }

    /**
     * Returns all calendar objects within a calendar.
     *
     * Every item contains an array with the following keys:
     *   * calendardata - The iCalendar-compatible calendar data
     *   * uri - a unique key which will be used to construct the uri. This can
     *     be any arbitrary string, but making sure it ends with '.ics' is a
     *     good idea. This is only the basename, or filename, not the full
     *     path.
     *   * lastmodified - a timestamp of the last modification time
     *   * etag - An arbitrary string, surrounded by double-quotes. (e.g.:
     *   '  "abcdef"')
     *   * size - The size of the calendar objects, in bytes.
     *   * component - optional, a string containing the type of object, such
     *     as 'vevent' or 'vtodo'. If specified, this will be used to populate
     *     the Content-Type header.
     *
     * Note that the etag is optional, but it's highly encouraged to return for
     * speed reasons.
     *
     * The calendardata is also optional. If it's not returned
     * 'getCalendarObject' will be called later, which *is* expected to return
     * calendardata.
     *
     * If neither etag or size are specified, the calendardata will be
     * used/fetched to determine these numbers. If both are specified the
     * amount of times this is needed is reduced by a great degree.
     *
     * @param mixed $calendarId
     *
     * @return array
     */
    public function getCalendarObjects($calendarId)
    {
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObjects.txt',print_r($calendarId,true));
        
        if (!is_array($calendarId)) {
            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        list($calendarId, $instanceId) = $calendarId;
        
        //mail('kostas@gks.gr', 'getCalendarObjects', $calendarId.'');
        
        $user_id=0;
        $other_myobj='';
        $stmt = $this->pdo->prepare("SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        //mail('kostas@gks.gr', '1', "SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
          $other_myobj=$row['other_myobj'];
          $user_id=$row['user_id'];
        }

        if ($other_myobj=='cal') {
          $stmt = $this->pdo->prepare('SELECT id_calendar, uri, mydate_edit, etag, calendar_user_id, size, componenttype FROM gks_calendar WHERE calendar_user_id = '.$user_id);
          //mail('kostas@gks.gr', '2', 'SELECT id_calendar, uri, mydate_edit, etag, calendar_user_id, size, componenttype FROM gks_calendar WHERE calendar_user_id = '.$user_id);
          $stmt->execute();
  
          $result = [];
          foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
              $result[] = [
                  'id' => $row['id_calendar'],
                  'uri' => $row['uri'],
                  'lastmodified' => (int) strtotime($row['mydate_edit']),
                  'etag' => '"'.$row['etag'].'"',
                  'size' => (int) $row['size'],
                  'component' => strtolower($row['componenttype']),
              ];
          }
          
          //print '<pre>';var_dump($result);die();
  
          return $result;
        }
        if ($other_myobj=='task') {
          $stmt = $this->pdo->prepare('SELECT gks_crm_tasks.id_crm_task, gks_crm_tasks.uri, gks_crm_tasks.mydate_edit, gks_crm_tasks.etag, gks_crm_tasks.size, gks_crm_tasks.componenttype
          FROM (
            SELECT gks_crm_tasks_employee.crm_task_id
            FROM gks_crm_tasks_employee
            WHERE gks_crm_tasks_employee.crm_task_employee_id= '.$user_id.'
            GROUP BY gks_crm_tasks_employee.crm_task_id
          )  AS user_on_task LEFT JOIN gks_crm_tasks ON user_on_task.crm_task_id = gks_crm_tasks.id_crm_task
          WHERE gks_crm_tasks.id_crm_task Is Not Null');

          $stmt->execute();
  
          $result = [];
          foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
              $result[] = [
                  'id' => $row['id_crm_task'],
                  'uri' => $row['uri'],
                  'lastmodified' => (int) strtotime($row['mydate_edit']),
                  'etag' => '"'.$row['etag'].'"',
                  'size' => (int) $row['size'],
                  'component' => strtolower($row['componenttype']),
              ];
          }
          
          return $result;
        }

        if ($other_myobj=='activity') {
          $stmt = $this->pdo->prepare("SELECT id_crm_activity, uri, mydate_edit, etag, size, componenttype
          FROM gks_crm_activity
          where activity_user_id=".$user_id."
          and uri<>''");

          $stmt->execute();
  
          $result = [];
          foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
              $result[] = [
                  'id' => $row['id_crm_activity'],
                  'uri' => $row['uri'],
                  'lastmodified' => (int) strtotime($row['mydate_edit']),
                  'etag' => '"'.$row['etag'].'"',
                  'size' => (int) $row['size'],
                  'component' => strtolower($row['componenttype']),
              ];
          }
          
          return $result;
        }
                
        if ($other_myobj=='transfer_reservation') {
          $stmt = $this->pdo->prepare('SELECT gks_transfer_reservation.id_transfer_reservation, gks_transfer_reservation.uri, gks_transfer_reservation.mydate_edit, gks_transfer_reservation.etag, gks_transfer_reservation.size, gks_transfer_reservation.componenttype
          FROM (
            SELECT gks_transfer_reservation_oximata.transfer_reservation_id
            FROM gks_transfer_reservation_oximata
            WHERE gks_transfer_reservation_oximata.transfer_oxima_driver_id= '.$user_id.'
            GROUP BY gks_transfer_reservation_oximata.transfer_reservation_id
          )  AS driver_on_transfer LEFT JOIN gks_transfer_reservation ON driver_on_transfer.transfer_reservation_id = gks_transfer_reservation.id_transfer_reservation
          WHERE gks_transfer_reservation.id_transfer_reservation Is Not Null');

          $stmt->execute();
  
          $result = [];
          foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
              $result[] = [
                  'id' => $row['id_transfer_reservation'],
                  'uri' => $row['uri'],
                  'lastmodified' => (int) strtotime($row['mydate_edit']),
                  'etag' => '"'.$row['etag'].'"',
                  'size' => (int) $row['size'],
                  'component' => strtolower($row['componenttype']),
              ];
          }
          
          return $result;
        }
        
    }

    /**
     * Returns information from a single calendar object, based on it's object
     * uri.
     *
     * The object uri is only the basename, or filename and not a full path.
     *
     * The returned array must have the same keys as getCalendarObjects. The
     * 'calendardata' object is required here though, while it's not required
     * for getCalendarObjects.
     *
     * This method must return null if the object did not exist.
     *
     * @param mixed  $calendarId
     * @param string $objectUri
     *
     * @return array|null
     */
    public function getCalendarObject($calendarId, $objectUri)
    {
        
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject.txt',print_r($calendarId,true).' '.$objectUri);
        
        if (!is_array($calendarId)) {
            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        list($calendarId, $instanceId) = $calendarId;
        
        //mail('kostas@gks.gr', 'getCalendarObject', $calendarId.'|'.$objectUri);
        
        //echo 'hhh';die();
        $user_id=0;
        $other_myobj='';
        $stmt = $this->pdo->prepare("SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        //mail('kostas@gks.gr', '3', "SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
          $other_myobj=$row['other_myobj'];
          $user_id=$row['user_id'];
        }

        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject '.$other_myobj.'.txt',print_r($calendarId,true).' '.$objectUri);
        
        if ($other_myobj=='cal') {
        	//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject in '.$other_myobj.'.txt',print_r($calendarId,true).' '.$objectUri);
          $sql="SELECT id_calendar, uri, mydate_edit, etag, calendar_user_id, size, calendardata, componenttype FROM gks_calendar WHERE calendar_user_id = ".$user_id." AND uri = '".$objectUri."'";
        	//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject in sql '.$other_myobj.'.txt',$sql);
          //mail('kostas@gks.gr', '4', $sql);
          
          $stmt = $this->pdo->prepare($sql);
          //$stmt->execute([$user_id, $objectUri]);
          $stmt->execute();
          $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        	//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject in sql exec '.$other_myobj.'.txt',$sql);
  
          if (!$row) {
              return null;
          }


          $ret=[
              'id' => $row['id_calendar'],
              'uri' => $row['uri'],
              'lastmodified' => (int) strtotime($row['mydate_edit']),
              'etag' => '"'.$row['etag'].'"',
              'size' => (int) $row['size'],
              'calendardata' => $row['calendardata'],
              'component' => strtolower($row['componenttype']),
           ];
          //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject found cal.txt',print_r($ret,true));
           
          return $ret;
        }
        
        if ($other_myobj=='task') {
        	//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject in '.$other_myobj.'.txt',print_r($calendarId,true).' '.$objectUri);

          $sql="SELECT id_crm_task, uri, gks_crm_tasks.mydate_edit, etag, size, calendardata, componenttype 
          FROM gks_crm_tasks 
          LEFT JOIN gks_crm_tasks_employee ON gks_crm_tasks.id_crm_task = gks_crm_tasks_employee.crm_task_id
          WHERE gks_crm_tasks_employee.crm_task_employee_id=".$user_id." AND gks_crm_tasks.uri = '".$objectUri."'";
          //mail('kostas@gks.gr', '4', $sql);

        	//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject sql.txt',$sql.' '.$objectUri);
          
          $stmt = $this->pdo->prepare($sql);
          //$stmt->execute([$user_id, $objectUri]);
          $stmt->execute();
          $row = $stmt->fetch(\PDO::FETCH_ASSOC);
  
          if (!$row) {
              return null;
          }
  
          $ret=[
              'id' => $row['id_crm_task'],
              'uri' => $row['uri'],
              'lastmodified' => (int) strtotime($row['mydate_edit']),
              'etag' => '"'.$row['etag'].'"',
              'size' => (int) $row['size'],
              'calendardata' => $row['calendardata'],
              'component' => strtolower($row['componenttype']),
           ];
          //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject found task.txt',print_r($ret,true));
           
          return $ret;          
          
          
        }
        
        if ($other_myobj=='activity') {
        	//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject in '.$other_myobj.'.txt',print_r($calendarId,true).' '.$objectUri);

          $sql="SELECT id_crm_activity, uri, mydate_edit, etag, size, calendardata, componenttype 
          FROM gks_crm_activity 
          WHERE activity_user_id=".$user_id." AND uri = '".$objectUri."'";
          //mail('kostas@gks.gr', '4', $sql);

        	//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject sql.txt',$sql.' '.$objectUri);
          
          $stmt = $this->pdo->prepare($sql);
          //$stmt->execute([$user_id, $objectUri]);
          $stmt->execute();
          $row = $stmt->fetch(\PDO::FETCH_ASSOC);
  
          if (!$row) {
              return null;
          }
  
          $ret=[
              'id' => $row['id_crm_activity'],
              'uri' => $row['uri'],
              'lastmodified' => (int) strtotime($row['mydate_edit']),
              'etag' => '"'.$row['etag'].'"',
              'size' => (int) $row['size'],
              'calendardata' => $row['calendardata'],
              'component' => strtolower($row['componenttype']),
           ];
          //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject found activity.txt',print_r($ret,true));
           
          return $ret;          
          
          
        }
                
        if ($other_myobj=='transfer_reservation') {
        	//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject in '.$other_myobj.'.txt',print_r($calendarId,true).' '.$objectUri);

          $sql="SELECT id_transfer_reservation, uri, gks_transfer_reservation.mydate_edit, etag, size, calendardata, componenttype 
          FROM gks_transfer_reservation 
          LEFT JOIN gks_transfer_reservation_oximata ON gks_transfer_reservation.id_transfer_reservation = gks_transfer_reservation_oximata.transfer_reservation_id
          WHERE gks_transfer_reservation_oximata.transfer_oxima_driver_id=".$user_id." AND gks_transfer_reservation.uri = '".$objectUri."'";
          //mail('kostas@gks.gr', '4', $sql);

        	//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject sql.txt',$sql.' '.$objectUri);
          
          $stmt = $this->pdo->prepare($sql);
          //$stmt->execute([$user_id, $objectUri]);
          $stmt->execute();
          $row = $stmt->fetch(\PDO::FETCH_ASSOC);
  
          if (!$row) {
              return null;
          }
  
          $ret=[
              'id' => $row['id_transfer_reservation'],
              'uri' => $row['uri'],
              'lastmodified' => (int) strtotime($row['mydate_edit']),
              'etag' => '"'.$row['etag'].'"',
              'size' => (int) $row['size'],
              'calendardata' => $row['calendardata'],
              'component' => strtolower($row['componenttype']),
           ];
          //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObject found transfer_reservation.txt',print_r($ret,true));
           
          return $ret;          
          
          
        }        
        
        
    }

    /**
     * Returns a list of calendar objects.
     *
     * This method should work identical to getCalendarObject, but instead
     * return all the calendar objects in the list as an array.
     *
     * If the backend supports this, it may allow for some speed-ups.
     *
     * @param mixed $calendarId
     *
     * @return array
     */
    public function getMultipleCalendarObjects($calendarId, array $uris)
    {
      
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getMultipleCalendarObjects.txt',print_r($calendarId,true).' '.print_r($uris,true));
      
        if (!is_array($calendarId)) {
            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        list($calendarId, $instanceId) = $calendarId;

        $user_id=0;
        $other_myobj='';
        $stmt = $this->pdo->prepare("SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        //mail('kostas@gks.gr', '18', "SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
          $other_myobj=$row['other_myobj'];
          $user_id=$row['user_id'];
        }
        

        $result = [];
        if ($other_myobj=='cal') {
          foreach (array_chunk($uris, 900) as $chunk) {
              $query = 'SELECT id_calendar, uri, mydate_edit, etag, calendar_user_id, size, calendardata, componenttype FROM gks_calendar WHERE calendar_user_id = ? AND uri IN (';
              // Inserting a whole bunch of question marks
              $query .= implode(',', array_fill(0, count($chunk), '?'));
              $query .= ')';
              //mail('kostas@gks.gr', '5', $query.' || '.print_r($chunk,true));
              $stmt = $this->pdo->prepare($query);
              $stmt->execute(array_merge([$user_id], $chunk));
  
              while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                  $result[] = [
                      'id' => $row['id_calendar'],
                      'uri' => $row['uri'],
                      'lastmodified' => (int) strtotime($row['mydate_edit']),
                      'etag' => '"'.$row['etag'].'"',
                      'size' => (int) $row['size'],
                      'calendardata' => $row['calendardata'],
                      'component' => strtolower($row['componenttype']),
                  ];
              }
          }
        }

        if ($other_myobj=='task') {
          foreach (array_chunk($uris, 900) as $chunk) {
              $query = 'SELECT id_crm_task, uri, gks_crm_tasks.mydate_edit, etag, size, calendardata, componenttype 
              FROM gks_crm_tasks 
              LEFT JOIN gks_crm_tasks_employee ON gks_crm_tasks.id_crm_task = gks_crm_tasks_employee.crm_task_id
              WHERE gks_crm_tasks_employee.crm_task_employee_id= ?  AND gks_crm_tasks.uri IN (';
              // Inserting a whole bunch of question marks
              $query .= implode(',', array_fill(0, count($chunk), '?'));
              $query .= ')';
              //mail('kostas@gks.gr', '5', $query.' || '.print_r($chunk,true));
              $stmt = $this->pdo->prepare($query);
              $stmt->execute(array_merge([$user_id], $chunk));
  
              while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                  $result[] = [
                      'id' => $row['id_crm_task'],
                      'uri' => $row['uri'],
                      'lastmodified' => (int) strtotime($row['mydate_edit']),
                      'etag' => '"'.$row['etag'].'"',
                      'size' => (int) $row['size'],
                      'calendardata' => $row['calendardata'],
                      'component' => strtolower($row['componenttype']),
                  ];
              }
          }
        }

        if ($other_myobj=='activity') {
          foreach (array_chunk($uris, 900) as $chunk) {
              $query = 'SELECT id_crm_activity, uri, mydate_edit, etag, size, calendardata, componenttype 
              FROM gks_crm_activity 
              WHERE activity_user_id= ?  AND uri IN (';
              // Inserting a whole bunch of question marks
              $query .= implode(',', array_fill(0, count($chunk), '?'));
              $query .= ')';
              //mail('kostas@gks.gr', '5', $query.' || '.print_r($chunk,true));
              $stmt = $this->pdo->prepare($query);
              $stmt->execute(array_merge([$user_id], $chunk));
  
              while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                  $result[] = [
                      'id' => $row['id_crm_activity'],
                      'uri' => $row['uri'],
                      'lastmodified' => (int) strtotime($row['mydate_edit']),
                      'etag' => '"'.$row['etag'].'"',
                      'size' => (int) $row['size'],
                      'calendardata' => $row['calendardata'],
                      'component' => strtolower($row['componenttype']),
                  ];
              }
          }
        }
        
        if ($other_myobj=='transfer_reservation') {
          foreach (array_chunk($uris, 900) as $chunk) {
              $query = 'SELECT id_transfer_reservation, uri, gks_transfer_reservation.mydate_edit, etag, size, calendardata, componenttype 
              FROM gks_transfer_reservation 
              LEFT JOIN gks_transfer_reservation_oximata ON gks_transfer_reservation.id_transfer_reservation = gks_transfer_reservation_oximata.transfer_reservation_id
              WHERE gks_transfer_reservation_oximata.transfer_oxima_driver_id= ?  AND gks_transfer_reservation.uri IN (';
              // Inserting a whole bunch of question marks
              $query .= implode(',', array_fill(0, count($chunk), '?'));
              $query .= ')';
              //mail('kostas@gks.gr', '5', $query.' || '.print_r($chunk,true));
              $stmt = $this->pdo->prepare($query);
              $stmt->execute(array_merge([$user_id], $chunk));
  
              while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                  $result[] = [
                      'id' => $row['id_transfer_reservation'],
                      'uri' => $row['uri'],
                      'lastmodified' => (int) strtotime($row['mydate_edit']),
                      'etag' => '"'.$row['etag'].'"',
                      'size' => (int) $row['size'],
                      'calendardata' => $row['calendardata'],
                      'component' => strtolower($row['componenttype']),
                  ];
              }
          }
        }
                        
        return $result;
    }

    /**
     * Creates a new calendar object.
     *
     * The object uri is only the basename, or filename and not a full path.
     *
     * It is possible return an etag from this function, which will be used in
     * the response to this PUT request. Note that the ETag must be surrounded
     * by double-quotes.
     *
     * However, you should only really return this ETag if you don't mangle the
     * calendar-data. If the result of a subsequent GET to this object is not
     * the exact same as this request body, you should omit the ETag.
     *
     * @param mixed  $calendarId
     * @param string $objectUri
     * @param string $calendarData
     *
     * @return string|null
     */
    public function createCalendarObject($calendarId, $objectUri, $calendarData)
    {
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' createCalendarObject.txt',print_r($calendarId,true).' '.$objectUri);
        
        if (!is_array($calendarId)) {
            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        list($calendarId, $instanceId) = $calendarId;

        $user_id=0;
        $other_myobj='';
        $stmt = $this->pdo->prepare("SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        //mail('kostas@gks.gr', '8', "SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
          $other_myobj=$row['other_myobj'];
          $user_id=$row['user_id'];
        }
        
        $extraData = $this->getDenormalizedData($calendarData);
        $gkIP = (isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '');
        
        if ($other_myobj=='cal') {
          $stmt = $this->pdo->prepare('INSERT INTO gks_calendar (calendar_user_id, uri, calendardata, mydate_add, mydate_edit, user_id_add,user_id_edit,myip,
          etag, size, componenttype, calendar_start, calendar_end, uid, 
          calendar_title,calendar_message,calendar_allday,calendar_odos
          ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
          $stmt->execute([
              $user_id,
              $objectUri,
              $calendarData,
              date('Y-m-d H:i:s'),
              date('Y-m-d H:i:s'),
              $user_id,
              $user_id,
              $gkIP,
              $extraData['etag'],
              $extraData['size'],
              $extraData['componentType'],
              date('Y-m-d H:i:s',$extraData['firstOccurence']),
              date('Y-m-d H:i:s',$extraData['lastOccurence']),
              $extraData['uid'],
              $extraData['calendar_title'],
              $extraData['calendar_message'],
              $extraData['calendar_allday'],
              $extraData['calendar_odos'],
          ]);
          $id_calendar = $this->pdo->lastInsertId();
          
          $this->addChange($calendarId, $objectUri, 1);
          
  
          foreach ($extraData['myalarms'] as $myalarm) {
  //          $diafora=$extraData['firstOccurence']-$myalarm['eftime'];
  //          $diafora=intval($diafora/60);
            
            $sql="INSERT INTO gks_calendar_notification (
            calendar_id,notification_number,notification_type,notification_unit,notification_rundate
            ) VALUES (".$id_calendar.",".$myalarm['number'].",'notif','".$myalarm['unit']."','".date('Y-m-d H:i:s',$myalarm['eftime'])."')";
            
            //mail('kostas@gks.gr', 'notif', $sql);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
  
          } 
          
          return '"'.$extraData['etag'].'"';
        }
        
        if ($other_myobj=='task') {
        	
          $stmt = $this->pdo->prepare('INSERT INTO gks_crm_tasks (uri, calendardata, mydate_add, mydate_edit, user_id_add,user_id_edit,myip,
          etag, size, componenttype, 
          task_date, task_planned_date_from, task_planned_date_to, uid, 
          subject,message,odos
          ) VALUES (?,?,?,?,?,?,?, ?,?,?, ?,?,?,?, ?,?,?)');
          $stmt->execute([

              $objectUri,
              $calendarData,
              date('Y-m-d H:i:s'),
              date('Y-m-d H:i:s'),
              $user_id,
              $user_id,
              $gkIP,
              
              $extraData['etag'],
              $extraData['size'],
              $extraData['componentType'],
              
              date('Y-m-d H:i:s'),
              date('Y-m-d H:i:s',$extraData['firstOccurence']),
              date('Y-m-d H:i:s',$extraData['lastOccurence']),
              $extraData['uid'],
              
              $extraData['calendar_title'],
              $extraData['calendar_message'],
              $extraData['calendar_odos'],
          ]);
          $id_crm_task = $this->pdo->lastInsertId();

          $sql="INSERT INTO gks_crm_tasks_employee (
          mydate_add, mydate_edit, user_id_add,user_id_edit,myip,
          crm_task_id,crm_task_employee_id
          ) VALUES (
          ?, ?, ? ,?, ?, 
          ?, ?)";
          $stmt = $this->pdo->prepare($sql);
          
          $stmt->execute([
          	date('Y-m-d H:i:s'),
          	date('Y-m-d H:i:s'),
          	$user_id,
          	$user_id,
          	$gkIP,
          	$id_crm_task,
          	$user_id,
          ]);
            
          $this->addChange($calendarId, $objectUri, 1);
          
          return '"'.$extraData['etag'].'"';
                  	
        }
        
        if ($other_myobj=='activity') {
        	
          $stmt = $this->pdo->prepare('INSERT INTO gks_crm_activity (uri, calendardata, mydate_add, mydate_edit, user_id_add,user_id_edit,myip,
          etag, size, componenttype, 
          activity_duedate, uid, 
          activity_subject,activity_message,
          activity_user_id
          ) VALUES (?,?,?,?,?,?,?, ?,?,?, ?,?,?,?, ?)');
          $stmt->execute([

              $objectUri,
              $calendarData,
              date('Y-m-d H:i:s'),
              date('Y-m-d H:i:s'),
              $user_id,
              $user_id,
              $gkIP,
              
              $extraData['etag'],
              $extraData['size'],
              $extraData['componentType'],
              
              date('Y-m-d H:i:s',$extraData['firstOccurence']),
              $extraData['uid'],
              
              $extraData['calendar_title'],
              $extraData['calendar_message'],
              $user_id,
          ]);
          $id_crm_task = $this->pdo->lastInsertId();

          
            
          $this->addChange($calendarId, $objectUri, 1);
          
          return '"'.$extraData['etag'].'"';
                  	
        }        
        
        return '';
    }

    /**
     * Updates an existing calendarobject, based on it's uri.
     *
     * The object uri is only the basename, or filename and not a full path.
     *
     * It is possible return an etag from this function, which will be used in
     * the response to this PUT request. Note that the ETag must be surrounded
     * by double-quotes.
     *
     * However, you should only really return this ETag if you don't mangle the
     * calendar-data. If the result of a subsequent GET to this object is not
     * the exact same as this request body, you should omit the ETag.
     *
     * @param mixed  $calendarId
     * @param string $objectUri
     * @param string $calendarData
     *
     * @return string|null
     */
    public function updateCalendarObject($calendarId, $objectUri, $calendarData)
    {
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' updateCalendarObject 1.txt',print_r($calendarId,true).' '.$objectUri);
      
        if (!is_array($calendarId)) {
            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        list($calendarId, $instanceId) = $calendarId;
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' updateCalendarObject '.$calendarId.'.txt',$calendarId.' '.$objectUri);

        //mail('kostas@gks.gr', '10', $calendarId.'||');
        
        $user_id=0;
        $other_myobj='';
        $stmt = $this->pdo->prepare("SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        //mail('kostas@gks.gr', '8', "SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
          $other_myobj=$row['other_myobj'];
          $user_id=$row['user_id'];
        }
        
        $extraData = $this->getDenormalizedData($calendarData);
        $gkIP = (isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '');
				if ($other_myobj=='cal') {
	        $stmt = $this->pdo->prepare('SELECT id_calendar,calendar_odos,calendar_perioxi,calendar_poli,calendar_tk,calendar_nomos_id,calendar_country_id FROM gks_calendar WHERE calendar_user_id = ? AND uri = ?');
	        $stmt->execute([$user_id, $objectUri]);
	        $id_calendar=0;
	        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	          $id_calendar = (int)$row['id_calendar'];
	          $calendar_odos = (string)$row['calendar_odos'];
	          $calendar_perioxi = (string)$row['calendar_perioxi'];
	          $calendar_poli = (string)$row['calendar_poli'];
	          $calendar_tk = (string)$row['calendar_tk'];
	          $calendar_nomos_id = (int)$row['calendar_nomos_id'];
	          $calendar_country_id = (int)$row['calendar_country_id'];
	        }
	        
	
	        if (is_null($calendar_odos)) $calendar_odos='';
	        if (is_null($calendar_perioxi)) $calendar_perioxi='';
	        if (is_null($calendar_poli)) $calendar_poli='';
	        if (is_null($calendar_tk)) $calendar_tk='';
	        if (is_null($calendar_nomos_id)) $calendar_nomos_id=0;
	        if (is_null($calendar_country_id)) $calendar_country_id=0;
	        
	        $calendar_odos=trim($calendar_odos);
	        $calendar_perioxi=trim($calendar_perioxi);
	        $calendar_poli=trim($calendar_poli);
	        $calendar_tk=trim($calendar_tk);
	        
	        //if (trim($calendar_odos)=='Ελλάδα') $calendar_odos='';
	        if ($calendar_country_id==0) $calendar_country_id=91;
	         
	        
	//        file_put_contents(GKS_SITE_PATH.'tmp/10.txt',$calendar_odos);
	//        file_put_contents(GKS_SITE_PATH.'tmp/11.txt',$calendar_perioxi);
	//        file_put_contents(GKS_SITE_PATH.'tmp/12.txt',$calendar_poli);
	//        file_put_contents(GKS_SITE_PATH.'tmp/13.txt',$calendar_tk);
	//        file_put_contents(GKS_SITE_PATH.'tmp/14.txt',$calendar_nomos_id);
	//        file_put_contents(GKS_SITE_PATH.'tmp/15.txt',$calendar_country_id);
	//        file_put_contents(GKS_SITE_PATH.'tmp/16.txt',$extraData['calendar_odos']);
	        
	        $update_odos=true;
	        if ($calendar_perioxi=='' and $calendar_poli=='' and $calendar_tk=='' and $calendar_nomos_id==0) {
	          $update_odos=true;
	        } else {
	          if ($calendar_odos !='' and 
	              strlen($extraData['calendar_odos']) >= strlen($calendar_odos) and
	              substr($extraData['calendar_odos'], 0, strlen($calendar_odos)) == $calendar_odos) 
	                $update_odos=false;
	        }
	        
	        $message = '';
					$temp=trim($extraData['calendar_message']);
					$pos1=strpos($temp, 'Περιγραφή: ');
					if ($pos1 !== false) {
						$temp=substr($temp, $pos1 +20);
						$pos1=strpos($temp, 'GEO: ');
						if ($pos1===false) { //den vretike
							$message=$temp;
						} else {
							$message=substr($temp, 0, $pos1-1);
						}
					}
					for($iii=1;$iii<=10;$iii++) {
						$message=str_replace(GKS_SITE_URL.'my/admin-crm-calendar.php?id='.$id_calendar."\n",'',$message);
					}
	        
	        $query='UPDATE gks_calendar SET 
	        calendardata = ?, 
	        mydate_edit = ?,
	        user_id_edit = ?, 
	        myip = ?, 
	        etag = ?, 
	        size = ?, 
	        componenttype = ?, 
	        calendar_start = ?, 
	        calendar_end = ?, 
	        uid = ?, 
	        calendar_title = ?, 
	        calendar_message = ?, '.
	        ($update_odos ? 'calendar_odos = ?, ' : '').
	        ' calendar_allday = ?  
	        WHERE calendar_user_id = ? AND uri = ?';
	        
	        $stmt = $this->pdo->prepare($query);
	        $mydata=[$calendarData, date('Y-m-d H:i:s'), $user_id, $gkIP, $extraData['etag'], 
	        $extraData['size'], $extraData['componentType'], 
	        date('Y-m-d H:i:s',$extraData['firstOccurence']), 
	        date('Y-m-d H:i:s',$extraData['lastOccurence']), 
	        $extraData['uid'], $extraData['calendar_title'], 
	        $message,  ];
	        //mail('kostas@gks.gr', '9', $query);
	        
	        if ($update_odos) $mydata[]=$extraData['calendar_odos'];
	        $mydata[]=$extraData['calendar_allday'];
	        $mydata[]=$user_id;
	        $mydata[]=$objectUri;
	        $stmt->execute($mydata);
	
					//file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' gg1.txt',$query.' '.print_r($mydata,true));
	        //file_put_contents('/var/www/php/www.gks.gr/httpdocs/my/gks_dav/CalDAV/Backend/gg1.txt',$query.' '.print_r($mydata,true));
	        $this->addChange($calendarId, $objectUri, 2);
	        
	        
	        $not_delete_records=array();
	        foreach ($extraData['myalarms'] as $myalarm) {
	//          $diafora=$extraData['firstOccurence']-$myalarm['eftime'];
	//          $diafora=intval($diafora/60);
	                    
	      		$sql="select id_calendar_notification 
	      		from gks_calendar_notification 
	      		where calendar_id= ?
	      		and notification_type = ?
	      		and notification_number= ?
	      		and notification_unit= ? ";
	      		$stmt = $this->pdo->prepare($sql);
	      		$stmt->execute([
	      		$id_calendar,
	      		'notif',
	      		$myalarm['number'],
	      		$myalarm['unit'],
	      		]);
	      		$number_of_rows = 0;  
	      		$id_calendar_notification=0;
	      		while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	      		  $number_of_rows++;
	      		  $id_calendar_notification=$row['id_calendar_notification'];
	      		  $not_delete_records[]=$id_calendar_notification;
	      		}
	      		//file_put_contents(GKS_SITE_PATH.'tmp/1.txt',$sql);
	      		if ($number_of_rows>=1) {
	      			//calendar_id,notification_type,notification_number,notification_unit,notification_rundate
	      			$sql="update gks_calendar_notification set notification_rundate='".date('Y-m-d H:i:s',$myalarm['eftime'])."' where id_calendar_notification=".$id_calendar_notification;
	            $stmt = $this->pdo->prepare($sql);
	      		  $stmt->execute();
	      			
	      		} else {
	      			$sql="insert into gks_calendar_notification (
	      			mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
	      			calendar_id,notification_type,notification_number,notification_unit,notification_rundate
	      			) values (
	      			now(),now(),".$user_id.",".$user_id.",'".$gkIP."',
	      			".$id_calendar.",'notif' ,".$myalarm['number'].",'".$myalarm['unit']."','".date('Y-m-d H:i:s',$myalarm['eftime'])."'
	      			)";
	      			//file_put_contents('/var/www/php/www.gks.gr/httpdocs/my/gks_dav/CalDAV/Backend/gg.txt',$sql);
	      			
	      			//mail('kostas@gks.gr', '115', $sql);
	      			//echo $sql;die();
	      			$stmt = $this->pdo->prepare($sql);
	      		  $stmt->execute();
	      			$id_calendar_notification = $this->pdo->lastInsertId();; 
	      			$not_delete_records[]=$id_calendar_notification;
	      		  //file_put_contents(GKS_SITE_PATH.'tmp/2.txt',$sql);
	      		}          
	          
	        }
	      	$sql="delete from gks_calendar_notification where calendar_id=".$id_calendar." and notification_type='notif'";
	      	if (count($not_delete_records)>0) $sql.=" and id_calendar_notification not in (".implode(',',$not_delete_records).")";
	  			$stmt = $this->pdo->prepare($sql);
	  		  $stmt->execute();
	        //file_put_contents(GKS_SITE_PATH.'tmp/3.txt',$sql);
	      }
	      
	      
	      if ($other_myobj=='task') {

	        $stmt = $this->pdo->prepare('SELECT id_crm_task 
	        FROM gks_crm_tasks WHERE uri = ? and id_crm_task in (select crm_task_id from gks_crm_tasks_employee where crm_task_employee_id = ? )');
	        $stmt->execute([$objectUri,$user_id]);
	        $id_crm_task=0;
	        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	          $id_crm_task = (int)$row['id_crm_task'];
	        }
	        
	        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' updateCalendarObject id_crm_task.txt',$id_crm_task.' '.$calendarId.' '.$objectUri);
	        
  //        file_put_contents(GKS_SITE_PATH.'tmp/10.txt',$id_crm_task);
	//        file_put_contents(GKS_SITE_PATH.'tmp/11.txt',$calendar_perioxi);
	//        file_put_contents(GKS_SITE_PATH.'tmp/12.txt',$calendar_poli);
	//        file_put_contents(GKS_SITE_PATH.'tmp/13.txt',$calendar_tk);
	//        file_put_contents(GKS_SITE_PATH.'tmp/14.txt',$calendar_nomos_id);
	//        file_put_contents(GKS_SITE_PATH.'tmp/15.txt',$calendar_country_id);
	//        file_put_contents(GKS_SITE_PATH.'tmp/16.txt',$extraData['calendar_odos']);
	        
					$message = '';
					$temp=trim($extraData['calendar_message']);
					$pos1=strpos($temp, 'Περιγραφή: ');
					if ($pos1 !== false) {
						$temp=substr($temp, $pos1 +20);
						$pos1=strpos($temp, 'GEO: ');
						if ($pos1===false) { //den vretike
							$message=$temp;
						} else {
							$message=substr($temp, 0, $pos1-1);
						}
					}
	        $message=str_replace(GKS_SITE_URL.'my/admin-crm-task-item.php?id='.$id_crm_task."\n",'',$message);
	        
	        $query='UPDATE gks_crm_tasks SET 
	        calendardata = ?, 
	        mydate_edit = ?,
	        user_id_edit = ?, 
	        myip = ?, 
	        etag = ?, 
	        size = ?, 
	        componenttype = ?, 
	        task_planned_date_from = ?, 
	        task_planned_date_to = ?,
	        subject= ?
	        '.($message!='' ? ',message=?' : '').'
	        WHERE id_crm_task = ? AND uri = ?';
	        
	        $stmt = $this->pdo->prepare($query);
	        $mydata=[
		        $calendarData, date('Y-m-d H:i:s'), $user_id, $gkIP, $extraData['etag'], 
		        $extraData['size'], $extraData['componentType'], 
		        date('Y-m-d H:i:s',$extraData['firstOccurence']), 
		        date('Y-m-d H:i:s',$extraData['lastOccurence']),
		        $extraData['calendar_title'], 
	        ];
	        if ($message!='') $mydata[]=$message;
	        
	        //mail('kostas@gks.gr', '9', $query);

	        $mydata[]=$id_crm_task;
	        $mydata[]=$objectUri;
	        $stmt->execute($mydata);
	
	        //file_put_contents('/var/www/php/www.gks.gr/httpdocs/my/gks_dav/CalDAV/Backend/gg1.txt',$query.' '.print_r($mydata,true));
	        $this->addChange($calendarId, $objectUri, 2);
	        	      	
	      	
	      }
	      
	      if ($other_myobj=='activity') {

	        $stmt = $this->pdo->prepare('SELECT id_crm_activity 
	        FROM gks_crm_activity WHERE uri = ? and activity_user_id= ?');
	        $stmt->execute([$objectUri,$user_id]);
	        $id_crm_activity=0;
	        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	          $id_crm_activity = (int)$row['id_crm_activity'];
	        }
	        
	        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' updateCalendarObject id_crm_task.txt',$id_crm_task.' '.$calendarId.' '.$objectUri);
	        
  //        file_put_contents(GKS_SITE_PATH.'tmp/10.txt',$id_crm_activity);
	//        file_put_contents(GKS_SITE_PATH.'tmp/11.txt',$calendar_perioxi);
	//        file_put_contents(GKS_SITE_PATH.'tmp/12.txt',$calendar_poli);
	//        file_put_contents(GKS_SITE_PATH.'tmp/13.txt',$calendar_tk);
	//        file_put_contents(GKS_SITE_PATH.'tmp/14.txt',$calendar_nomos_id);
	//        file_put_contents(GKS_SITE_PATH.'tmp/15.txt',$calendar_country_id);
	//        file_put_contents(GKS_SITE_PATH.'tmp/16.txt',$extraData['calendar_odos']);
	        
          $message = '';
					$temp=trim($extraData['calendar_message']);
					$pos1=strpos($temp, 'Περιγραφή: ');
					if ($pos1 !== false) {
						$temp=substr($temp, $pos1 +20);
						$pos1=strpos($temp, 'GEO: ');
						if ($pos1===false) { //den vretike
							$message=$temp;
						} else {
							$message=substr($temp, 0, $pos1-1);
						}
					}
	        $message=str_replace(GKS_SITE_URL.'my/admin-crm-activity-item.php?id='.$id_crm_activity."\n",'',$message);
	        
	        	        
	        $query='UPDATE gks_crm_activity SET 
	        calendardata = ?, 
	        mydate_edit = ?,
	        user_id_edit = ?, 
	        myip = ?, 
	        etag = ?, 
	        size = ?, 
	        componenttype = ?, 
	        activity_duedate = ?, 
	        activity_subject= ?
	        '.($message!='' ? ',activity_message=?' : '').'
	        WHERE id_crm_activity = ? AND uri = ?';
	        
	        $stmt = $this->pdo->prepare($query);
	        $mydata=[
		        $calendarData, date('Y-m-d H:i:s'), $user_id, $gkIP, $extraData['etag'], 
		        $extraData['size'], $extraData['componentType'], 
		        date('Y-m-d H:i:s',$extraData['firstOccurence']), 
		        $extraData['calendar_title'], 
	        ];
	        if ($message!='') $mydata[]=$message;
	        
	        //mail('kostas@gks.gr', '9', $query);

	        $mydata[]=$id_crm_activity;
	        $mydata[]=$objectUri;
	        $stmt->execute($mydata);
	
	        //file_put_contents('/var/www/php/www.gks.gr/httpdocs/my/gks_dav/CalDAV/Backend/gg1.txt',$query.' '.print_r($mydata,true));
	        $this->addChange($calendarId, $objectUri, 2);
	        	      	
	      	
	      }	      
        
        //echo time();

        return '"'.$extraData['etag'].'"';
    }

    /**
     * Parses some information from calendar objects, used for optimized
     * calendar-queries.
     *
     * Returns an array with the following keys:
     *   * etag - An md5 checksum of the object without the quotes.
     *   * size - Size of the object in bytes
     *   * componentType - VEVENT, VTODO or VJOURNAL
     *   * firstOccurence
     *   * lastOccurence
     *   * uid - value of the UID property
     *
     * @param string $calendarData
     *
     * @return array
     */
    protected function getDenormalizedData($calendarData)
    {
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getDenormalizedData.txt',$calendarData);
        
        $vObject = VObject\Reader::read($calendarData);
        $componentType = null;
        $component = null;
        $firstOccurence = null;
        $lastOccurence = null;
        $uid = null;
        $calendar_title='';
        $calendar_message='';
        $calendar_allday=0;
        $calendar_odos='';
        $myalarms=array();
        

        
                
        foreach ($vObject->getComponents() as $component) {
            if ('VTIMEZONE' !== $component->name) {
                $componentType = $component->name;
                $uid = (string) $component->UID;
                break;
            }
        }
        if (!$componentType) {
            throw new \Sabre\DAV\Exception\BadRequest('Calendar objects must have a VJOURNAL, VEVENT or VTODO component');
        }
        if ('VEVENT' === $componentType) {
            $firstOccurence = $component->DTSTART->getDateTime()->getTimeStamp();
            // Finding the last occurence is a bit harder
            if (!isset($component->RRULE)) {
                if (isset($component->DTEND)) {
                    $lastOccurence = $component->DTEND->getDateTime()->getTimeStamp();
                } elseif (isset($component->DURATION)) {
                    $endDate = clone $component->DTSTART->getDateTime();
                    $endDate = $endDate->add(VObject\DateTimeParser::parse($component->DURATION->getValue()));
                    $lastOccurence = $endDate->getTimeStamp();
                } elseif (!$component->DTSTART->hasTime()) {
                    $endDate = clone $component->DTSTART->getDateTime();
                    $endDate = $endDate->modify('+1 day');
                    $lastOccurence = $endDate->getTimeStamp();
                } else {
                    $lastOccurence = $firstOccurence;
                }
            } else {
                $it = new VObject\Recur\EventIterator($vObject, (string) $component->UID);
                $maxDate = new \DateTime(self::MAX_DATE);
                if ($it->isInfinite()) {
                    $lastOccurence = $maxDate->getTimeStamp();
                } else {
                    $end = $it->getDtEnd();
                    while ($it->valid() && $end < $maxDate) {
                        $end = $it->getDtEnd();
                        $it->next();
                    }
                    $lastOccurence = $end->getTimeStamp();
                }
            }

            // Ensure Occurence values are positive
            if ($firstOccurence < 0) {
                $firstOccurence = 0;
            }
            if ($lastOccurence < 0) {
                $lastOccurence = 0;
            }
            
            if (date('H:i:s',$firstOccurence)=='00:00:00' and date('H:i:s',$lastOccurence)=='00:00:00') {
              $calendar_allday=1;
              $firstOccurence = new \DateTime(date('Y-m-d H:i:s', $firstOccurence), new \DateTimeZone('Europe/Athens'));
              $firstOccurence->setTimezone(new \DateTimeZone('UTC'));
              $firstOccurence = strtotime($firstOccurence->format('Y-m-d H:i:s'));

              $lastOccurence = new \DateTime(date('Y-m-d H:i:s', $lastOccurence), new \DateTimeZone('Europe/Athens'));
              $lastOccurence->setTimezone(new \DateTimeZone('UTC'));
              $lastOccurence = strtotime($lastOccurence->format('Y-m-d H:i:s'));

            }
            
            
            
            $calendar_title=''; if (isset($component->SUMMARY)) $calendar_title=mb_substr(trim($component->SUMMARY->getValue()),0,250);
            //echo '<pre>';
            //var_dump($component->SUMMARY);//die();
            //file_put_contents(GKS_SITE_PATH.'tmp/3.txt',$calendar_title);
            
            $calendar_message=''; if (isset($component->DESCRIPTION)) $calendar_message=mb_substr(trim($component->DESCRIPTION->getValue()),0,64000);
            $calendar_odos=''; if (isset($component->LOCATION)) $calendar_odos=mb_substr(trim($component->LOCATION->getValue()),0,250);
            if (trim($calendar_odos)=='Ελλάδα') $calendar_odos='';
            

            
        

          foreach ($vObject->getComponents() as $component) {
            if ('VEVENT' === $component->name) {
              foreach ($component->getComponents() as $component2) {
                if ('VALARM' === $component2->name) {
                  $descr=''; if (isset($component2->DESCRIPTION)) $descr=trim($component2->DESCRIPTION->getValue());
                  $notification_rundate=$component2->getEffectiveTriggerTime()->getTimeStamp();
                  $diafora=$firstOccurence - $notification_rundate;
                  
                  $notification_unit='minute';
                  $notification_number=$notification_number=intval($diafora/(60));
                  
                  if (($diafora % (24*60*60)) == 0) {$notification_unit = 'day'; $notification_number=intval($diafora/(24*60*60));}
                  else if (($diafora % (60*60)) == 0) {$notification_unit = 'hour'; $notification_number=intval($diafora/(60*60));}
                  
                  
                  
                  $myalarms[]=array(
                    'descr' => $descr, 
                    'eftime' => $component2->getEffectiveTriggerTime()->getTimeStamp(),
                    'unit' => $notification_unit,
                    'number' => $notification_number,
                  );
                }
              }
              break;
            }
          }
        
        }
        
        // Destroy circular references to PHP will GC the object.
        $vObject->destroy();

        return [
            'etag' => md5($calendarData),
            'size' => strlen($calendarData),
            'componentType' => $componentType,
            'firstOccurence' => $firstOccurence,
            'lastOccurence' => $lastOccurence,
            'uid' => $uid,
            'calendar_title' => $calendar_title,
            'calendar_message' => $calendar_message,
            'calendar_allday' => $calendar_allday,
            'calendar_odos' => $calendar_odos,
            'myalarms' => $myalarms,

        ];
    }

    /**
     * Deletes an existing calendar object.
     *
     * The object uri is only the basename, or filename and not a full path.
     *
     * @param mixed  $calendarId
     * @param string $objectUri
     */
    public function deleteCalendarObject($calendarId, $objectUri)
    {
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' deleteCalendarObject 1.txt',print_r($calendarId,true).' '.$objectUri);
        
        if (!is_array($calendarId)) {
            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        list($calendarId, $instanceId) = $calendarId;
        
        $user_id=0;
        $other_myobj='';
        $stmt = $this->pdo->prepare("SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        //mail('kostas@gks.gr', '8', "SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
          $other_myobj=$row['other_myobj'];
          $user_id=$row['user_id'];
        }

        
        //mail('kostas@gks.gr', 'deleteCalendarObject', $calendarId.'|'.$objectUri);
        if ($other_myobj=='cal') {
          $stmt = $this->pdo->prepare('DELETE FROM gks_calendar WHERE calendar_user_id = ? AND uri = ?');
          $stmt->execute([$user_id, $objectUri]);
  
          $this->addChange($calendarId, $objectUri, 3);
        }
    }

    /**
     * Performs a calendar-query on the contents of this calendar.
     *
     * The calendar-query is defined in RFC4791 : CalDAV. Using the
     * calendar-query it is possible for a client to request a specific set of
     * object, based on contents of iCalendar properties, date-ranges and
     * iCalendar component types (VTODO, VEVENT).
     *
     * This method should just return a list of (relative) urls that match this
     * query.
     *
     * The list of filters are specified as an array. The exact array is
     * documented by \Sabre\CalDAV\CalendarQueryParser.
     *
     * Note that it is extremely likely that getCalendarObject for every path
     * returned from this method will be called almost immediately after. You
     * may want to anticipate this to speed up these requests.
     *
     * This method provides a default implementation, which parses *all* the
     * iCalendar objects in the specified calendar.
     *
     * This default may well be good enough for personal use, and calendars
     * that aren't very large. But if you anticipate high usage, big calendars
     * or high loads, you are strongly adviced to optimize certain paths.
     *
     * The best way to do so is override this method and to optimize
     * specifically for 'common filters'.
     *
     * Requests that are extremely common are:
     *   * requests for just VEVENTS
     *   * requests for just VTODO
     *   * requests with a time-range-filter on a VEVENT.
     *
     * ..and combinations of these requests. It may not be worth it to try to
     * handle every possible situation and just rely on the (relatively
     * easy to use) CalendarQueryValidator to handle the rest.
     *
     * Note that especially time-range-filters may be difficult to parse. A
     * time-range filter specified on a VEVENT must for instance also handle
     * recurrence rules correctly.
     * A good example of how to interpret all these filters can also simply
     * be found in \Sabre\CalDAV\CalendarQueryFilter. This class is as correct
     * as possible, so it gives you a good idea on what type of stuff you need
     * to think of.
     *
     * This specific implementation (for the PDO) backend optimizes filters on
     * specific components, and VEVENT time-ranges.
     *
     * @param mixed $calendarId
     *
     * @return array
     */
    public function calendarQuery($calendarId, array $filters)
    {
      
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' calendarQuery 1.txt',print_r($calendarId,true).' '.print_r($filters,true));


        if (!is_array($calendarId)) {
            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        list($calendarId, $instanceId) = $calendarId;
        
        //mail('kostas@gks.gr', 'calendarQuery', $calendarId.'|'.print_r($filters,true));
        $user_id=0;
        $other_myobj='';
        $stmt = $this->pdo->prepare("SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
        //$stmt->execute([$user_id]);
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
          $caldav_synctoken = $row['caldav_synctoken'];
          $other_myobj=$row['other_myobj'];
          $user_id=$row['user_id'];
        }


        $componentType = null;
        $requirePostFilter = true;
        $timeRange = null;

        // if no filters were specified, we don't need to filter after a query
        if (!$filters['prop-filters'] && !$filters['comp-filters']) {
            $requirePostFilter = false;
        }

        // Figuring out if there's a component filter
        if (count($filters['comp-filters']) > 0 && !$filters['comp-filters'][0]['is-not-defined']) {
            $componentType = $filters['comp-filters'][0]['name'];

            // Checking if we need post-filters
            $has_time_range = array_key_exists('time-range', $filters['comp-filters'][0]) && $filters['comp-filters'][0]['time-range'];
            if (!$filters['prop-filters'] && !$filters['comp-filters'][0]['comp-filters'] && !$has_time_range && !$filters['comp-filters'][0]['prop-filters']) {
                $requirePostFilter = false;
            }
            // There was a time-range filter
            if ('VEVENT' == $componentType && $has_time_range) {
                $timeRange = $filters['comp-filters'][0]['time-range'];

                // If start time OR the end time is not specified, we can do a
                // 100% accurate mysql query.
                if (!$filters['prop-filters'] && !$filters['comp-filters'][0]['comp-filters'] && !$filters['comp-filters'][0]['prop-filters'] && $timeRange) {
                    if ((array_key_exists('start', $timeRange) && !$timeRange['start']) || (array_key_exists('end', $timeRange) && !$timeRange['end'])) {
                        $requirePostFilter = false;
                    }
                }
            }
        }
				
				if ($other_myobj=='cal') {
				
	        if ($requirePostFilter) {
	            $query = 'SELECT uri, calendardata FROM gks_calendar WHERE calendar_user_id = :calendar_user_id';
	        } else {
	            $query = 'SELECT uri FROM gks_calendar WHERE calendar_user_id = :calendar_user_id';
	        }
	        //file_put_contents(GKS_SITE_PATH.'tmp/1.txt',$query);
	        
	        $values = [
	            'calendar_user_id' => $user_id,
	        ];
	
	        if ($componentType) {
	            $query .= ' AND componenttype = :componenttype';
	            $values['componenttype'] = $componentType;
	        }
	
	        if ($timeRange && array_key_exists('start', $timeRange) && $timeRange['start']) {
	            $query .= ' AND calendar_end > :startdate';
	            $values['startdate'] = date('Y-m-d H:i:s',$timeRange['start']->getTimeStamp());
	        }
	        if ($timeRange && array_key_exists('end', $timeRange) && $timeRange['end']) {
	            $query .= ' AND calendar_start < :enddate';
	            $values['enddate'] = date('Y-m-d H:i:s',$timeRange['end']->getTimeStamp());
	        }
	        
	        $stmt = $this->pdo->prepare($query);
	        $stmt->execute($values);
	
	        $result = [];
	        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	            if ($requirePostFilter) {
	                if (!$this->validateFilterForObject($row, $filters)) {
	                    continue;
	                }
	            }
	            $result[] = $row['uri'];
	        }	        
	      }
	      if ($other_myobj=='task') {
	        if ($requirePostFilter) {
	            $query = 'SELECT uri, calendardata 
		          FROM (
		            SELECT gks_crm_tasks_employee.crm_task_id
		            FROM gks_crm_tasks_employee
		            WHERE gks_crm_tasks_employee.crm_task_employee_id= :calendar_user_id
		            GROUP BY gks_crm_tasks_employee.crm_task_id
		          )  AS user_on_task LEFT JOIN gks_crm_tasks ON user_on_task.crm_task_id = gks_crm_tasks.id_crm_task
		          WHERE gks_crm_tasks.id_crm_task Is Not Null';
	        } else {
	            $query = 'SELECT uri 
		          FROM (
		            SELECT gks_crm_tasks_employee.crm_task_id
		            FROM gks_crm_tasks_employee
		            WHERE gks_crm_tasks_employee.crm_task_employee_id= :calendar_user_id
		            GROUP BY gks_crm_tasks_employee.crm_task_id
		          )  AS user_on_task LEFT JOIN gks_crm_tasks ON user_on_task.crm_task_id = gks_crm_tasks.id_crm_task
		          WHERE gks_crm_tasks.id_crm_task Is Not Null';
	        }
	        //file_put_contents(GKS_SITE_PATH.'tmp/1.txt',$query);
	        
	        $values = [
	            'calendar_user_id' => $user_id,
	        ];
	
	        if ($componentType) {
	            $query .= ' AND componenttype = :componenttype';
	            $values['componenttype'] = $componentType;
	        }
	
	        if ($timeRange && array_key_exists('start', $timeRange) && $timeRange['start']) {
	            $query .= ' AND task_planned_date_to > :startdate';
	            $values['startdate'] = date('Y-m-d H:i:s',$timeRange['start']->getTimeStamp());
	        }
	        if ($timeRange && array_key_exists('end', $timeRange) && $timeRange['end']) {
	            $query .= ' AND task_planned_date_from < :enddate';
	            $values['enddate'] = date('Y-m-d H:i:s',$timeRange['end']->getTimeStamp());
	        }
	        
	        $stmt = $this->pdo->prepare($query);
	        $stmt->execute($values);
	
	        $result = [];
	        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	            if ($requirePostFilter) {
	                if (!$this->validateFilterForObject($row, $filters)) {
	                    continue;
	                }
	            }
	            $result[] = $row['uri'];
	        }	
	      }

	      if ($other_myobj=='activity') {
	        if ($requirePostFilter) {
	            $query = 'SELECT uri, calendardata 
		          FROM gks_crm_activity
		          WHERE activity_user_id = :activity_user_id ';
	        } else {
	            $query = 'SELECT uri 
		          FROM gks_crm_activity
		          WHERE activity_user_id = :activity_user_id ';
	        }
	        //file_put_contents(GKS_SITE_PATH.'tmp/1.txt',$query);
	        
	        $values = [
	            'activity_user_id' => $user_id,
	        ];
	
	        if ($componentType) {
	            $query .= ' AND componenttype = :componenttype';
	            $values['componenttype'] = $componentType;
	        }
	
	        if ($timeRange && array_key_exists('start', $timeRange) && $timeRange['start']) {
	            $query .= ' AND activity_duedate >= :startdate';
	            $values['startdate'] = date('Y-m-d H:i:s',$timeRange['start']->getTimeStamp());
	        }
	        if ($timeRange && array_key_exists('end', $timeRange) && $timeRange['end']) {
	            $query .= ' AND activity_duedate <= :enddate';
	            $values['enddate'] = date('Y-m-d H:i:s',$timeRange['end']->getTimeStamp());
	        }
	        
	        $stmt = $this->pdo->prepare($query);
	        $stmt->execute($values);
	
	        $result = [];
	        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	            if ($requirePostFilter) {
	                if (!$this->validateFilterForObject($row, $filters)) {
	                    continue;
	                }
	            }
	            $result[] = $row['uri'];
	        }	
	      }
	      	      
        //file_put_contents(GKS_SITE_PATH.'tmp/2.txt',$query. print_r($values,true));

	      if ($other_myobj=='transfer_reservation') {
	        if ($requirePostFilter) {
	            $query = 'SELECT uri, calendardata 
		          FROM (
		            SELECT gks_transfer_reservation_oximata.transfer_reservation_id
		            FROM gks_transfer_reservation_oximata
		            WHERE gks_transfer_reservation_oximata.transfer_oxima_driver_id= :calendar_user_id
		            GROUP BY gks_transfer_reservation_oximata.transfer_reservation_id
		          )  AS driver_on_transfer LEFT JOIN gks_transfer_reservation ON driver_on_transfer.transfer_reservation_id = gks_transfer_reservation.id_transfer_reservation
		          WHERE gks_transfer_reservation.id_transfer_reservation Is Not Null';
	        } else {
	            $query = 'SELECT uri 
		          FROM (
		            SELECT gks_transfer_reservation_oximata.transfer_reservation_id
		            FROM gks_transfer_reservation_oximata
		            WHERE gks_transfer_reservation_oximata.transfer_oxima_driver_id= :calendar_user_id
		            GROUP BY gks_transfer_reservation_oximata.transfer_reservation_id
		          )  AS driver_on_transfer LEFT JOIN gks_transfer_reservation ON driver_on_transfer.transfer_reservation_id = gks_transfer_reservation.id_transfer_reservation
		          WHERE gks_transfer_reservation.id_transfer_reservation Is Not Null';
	        }
	        //file_put_contents(GKS_SITE_PATH.'tmp/1.txt',$query);
	        
	        $values = [
	            'calendar_user_id' => $user_id,
	        ];
	
	        if ($componentType) {
	            $query .= ' AND componenttype = :componenttype';
	            $values['componenttype'] = $componentType;
	        }
	
	        if ($timeRange && array_key_exists('start', $timeRange) && $timeRange['start']) {
	            $query .= ' AND transfer_start > :startdate';
	            $values['startdate'] = date('Y-m-d H:i:s',$timeRange['start']->getTimeStamp());
	        }
	        if ($timeRange && array_key_exists('end', $timeRange) && $timeRange['end']) {
	            $query .= ' AND transfer_end < :enddate';
	            $values['enddate'] = date('Y-m-d H:i:s',$timeRange['end']->getTimeStamp());
	        }
	        
	        $stmt = $this->pdo->prepare($query);
	        $stmt->execute($values);
	
	        $result = [];
	        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	            if ($requirePostFilter) {
	                if (!$this->validateFilterForObject($row, $filters)) {
	                    continue;
	                }
	            }
	            $result[] = $row['uri'];
	        }	
	      }
	      
	      				
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' calendarQuery result.txt',print_r($result,true));
				
				
				


        return $result;
    }

    /**
     * Searches through all of a users calendars and calendar objects to find
     * an object with a specific UID.
     *
     * This method should return the path to this object, relative to the
     * calendar home, so this path usually only contains two parts:
     *
     * calendarpath/objectpath.ics
     *
     * If the uid is not found, return null.
     *
     * This method should only consider * objects that the principal owns, so
     * any calendars owned by other principals that also appear in this
     * collection should be ignored.
     *
     * @param string $principalUri
     * @param string $uid
     *
     * @return string|null
     */
    public function getCalendarObjectByUID($principalUri, $uid)
    {
      //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getCalendarObjectByUID.txt',$principalUri.' '.$uid);
      print '<pre>';var_dump($principalUri);var_dump($uid);die();
      
        $query = <<<SQL
SELECT
    calendar_instances.uri AS calendaruri, calendarobjects.uri as objecturi
FROM
    $this->calendarObjectTableName AS calendarobjects
LEFT JOIN
    $this->calendarInstancesTableName AS calendar_instances
    ON calendarobjects.calendarid = calendar_instances.calendarid
WHERE
    calendar_instances.principaluri = ?
    AND
    calendarobjects.uid = ?
    AND
    calendar_instances.access = 1
SQL;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$principalUri, $uid]);

        
        
        
        if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            echo '<pre>'.$row['calendaruri'].'/'.$row['objecturi'];
            die();
            return $row['calendaruri'].'/'.$row['objecturi'];
        }
    }

    /**
     * The getChanges method returns all the changes that have happened, since
     * the specified syncToken in the specified calendar.
     *
     * This function should return an array, such as the following:
     *
     * [
     *   'syncToken' => 'The current synctoken',
     *   'added'   => [
     *      'new.txt',
     *   ],
     *   'modified'   => [
     *      'modified.txt',
     *   ],
     *   'deleted' => [
     *      'foo.php.bak',
     *      'old.txt'
     *   ]
     * ];
     *
     * The returned syncToken property should reflect the *current* syncToken
     * of the calendar, as reported in the {http://sabredav.org/ns}sync-token
     * property this is needed here too, to ensure the operation is atomic.
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
     * @param mixed  $calendarId
     * @param string $syncToken
     * @param int    $syncLevel
     * @param int    $limit
     *
     * @return array
     */
    public function getChangesForCalendar($calendarId, $syncToken, $syncLevel, $limit = null)
    {
        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' getChangesForCalendar 1'.rand(1000,9999).'.txt',print_r($calendarId,true).' '.$syncToken.' '.$syncLevel.' '.$limit);
        
        if (!is_array($calendarId)) {
            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
        }
        list($calendarId, $instanceId) = $calendarId;

        //mail('kostas@gks.gr', 'getChangesForCalendar', $calendarId.'|'.$syncToken.'|'.$syncLevel);
        // Current synctoken
        $sql='SELECT caldav_synctoken FROM gks_calendar_dav_calendars WHERE id_dav_calendar = '.$calendarId;
        $stmt = $this->pdo->prepare($sql);
        //mail('kostas@gks.gr', '6', $sql);
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
            //$query = 'SELECT uri, operation FROM gks_calendar_dav_changes WHERE synctoken >= '.$syncToken.' AND synctoken < '.$currentToken.' AND calendarid = '.$calendarId.' ORDER BY synctoken';
            $query = 'SELECT uri, operation, synctoken FROM gks_calendar_dav_changes WHERE synctoken >= '.$syncToken.' AND calendarid = '.$calendarId.' ORDER BY synctoken';
            if ($limit > 0) {
            	  // Fetch one more raw to detect result truncation
                $query .= ' LIMIT '.((int) $limit + 1);
            }
            //mail('kostas@gks.gr', '7', $query);
            // Fetching all changes
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();

            $changes = [];

            // This loop ensures that any duplicates are overwritten, only the
            // last change on a node is relevant.
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $changes[$row['uri']] = $row;
            }
            $currentToken = null;

            $result_count = 0;
            foreach ($changes as $uri => $operation) {
                if (!is_null($limit) && $result_count >= $limit) {
                    $result['result_truncated'] = true;
                    break;
                }

                if (null === $currentToken || $currentToken < $operation['synctoken'] + 1) {
                    // SyncToken in CalDAV perspective is consistently the next number of the last synced change event in this class.
                    $currentToken = $operation['synctoken'] + 1;
                }

                ++$result_count;
                switch ($operation['operation']) {
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

            if (!is_null($currentToken)) {
                $result['syncToken'] = $currentToken;
            } else {
                // This means returned value is equivalent to syncToken
                $result['syncToken'] = $syncToken;
            }
        } else {
            // No synctoken supplied, this is the initial sync.
            $user_id=0;
            $other_myobj='';
            $stmt = $this->pdo->prepare("SELECT * FROM gks_calendar_dav_calendars where id_dav_calendar=".$calendarId);
            $stmt->execute([$user_id]);
            $currentToken=null;
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
              $currentToken = $row['caldav_synctoken'];
              $other_myobj=$row['other_myobj'];
              $user_id=$row['user_id'];
            }
            if (is_null($currentToken)) {
                return null;
            }
            $result['syncToken'] = $currentToken;
            
            if ($other_myobj=='cal') {
              $query = 'SELECT uri FROM gks_calendar WHERE calendar_user_id = '.$user_id;
              $stmt = $this->pdo->prepare($query);
              $stmt->execute();
  
              $result['added'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);
              
            } else if ($other_myobj=='task') {
              
              $query = 'SELECT uri 
              FROM gks_crm_tasks 
              LEFT JOIN gks_crm_tasks_employee ON gks_crm_tasks.id_crm_task = gks_crm_tasks_employee.crm_task_id
              WHERE gks_crm_tasks_employee.crm_task_employee_id= '.$user_id;
              $stmt = $this->pdo->prepare($query);
              $stmt->execute();
  
              $result['added'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            } else if ($other_myobj=='task') {
              
              $query = 'SELECT uri 
              FROM gks_crm_tasks 
              LEFT JOIN gks_crm_tasks_employee ON gks_crm_tasks.id_crm_task = gks_crm_tasks_employee.crm_task_id
              WHERE gks_crm_tasks_employee.crm_task_employee_id= '.$user_id;
              $stmt = $this->pdo->prepare($query);
              $stmt->execute();
  
              $result['added'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            } else if ($other_myobj=='activity') {
              
              $query = 'SELECT uri 
              FROM gks_crm_activity 
              WHERE activity_user_id= '.$user_id;
              $stmt = $this->pdo->prepare($query);
              $stmt->execute();
  
              $result['added'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            } else if ($other_myobj=='transfer_reservation') {
              
              $query = 'SELECT uri 
              FROM gks_transfer_reservation 
              LEFT JOIN gks_transfer_reservation_oximata ON gks_transfer_reservation.id_transfer_reservation = gks_transfer_reservation_oximata.transfer_reservation_id
              WHERE gks_transfer_reservation_oximata.transfer_oxima_driver_id= '.$user_id;
              $stmt = $this->pdo->prepare($query);
              $stmt->execute();
  
              $result['added'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);
              
            }
        }

        return $result;
    }

    /**
     * Adds a change record to the calendarchanges table.
     *
     * @param mixed  $calendarId
     * @param string $objectUri
     * @param int    $operation  1 = add, 2 = modify, 3 = delete
     */
    protected function addChange($calendarId, $objectUri, $operation)
    {
        //mail('kostas@gks.gr', 'addChange', $calendarId.'|'.$objectUri.'|'.$operation);

        //file_put_contents(GKS_SITE_PATH.'tmp/'.microtime(true).' addChange '.rand(1000,9999).'.txt',print_r($calendarId,true).' '.$objectUri.' '.$operation);

        
        $stmt = $this->pdo->prepare('INSERT INTO gks_calendar_dav_changes (uri, synctoken, calendarid, operation) SELECT ?, caldav_synctoken, ?, ? FROM gks_calendar_dav_calendars WHERE id_dav_calendar = ?');
        $stmt->execute([
            $objectUri,
            $calendarId,
            $operation,
            $calendarId,
        ]);
        
        
        $stmt = $this->pdo->prepare('UPDATE gks_calendar_dav_calendars SET caldav_synctoken = caldav_synctoken + 1 WHERE id_dav_calendar = ?');
        $stmt->execute([
            $calendarId,
        ]);
    }

    /**
     * Returns a list of subscriptions for a principal.
     *
     * Every subscription is an array with the following keys:
     *  * id, a unique id that will be used by other functions to modify the
     *    subscription. This can be the same as the uri or a database key.
     *  * uri. This is just the 'base uri' or 'filename' of the subscription.
     *  * principaluri. The owner of the subscription. Almost always the same as
     *    principalUri passed to this method.
     *  * source. Url to the actual feed
     *
     * Furthermore, all the subscription info must be returned too:
     *
     * 1. {DAV:}displayname
     * 2. {http://apple.com/ns/ical/}refreshrate
     * 3. {http://calendarserver.org/ns/}subscribed-strip-todos (omit if todos
     *    should not be stripped).
     * 4. {http://calendarserver.org/ns/}subscribed-strip-alarms (omit if alarms
     *    should not be stripped).
     * 5. {http://calendarserver.org/ns/}subscribed-strip-attachments (omit if
     *    attachments should not be stripped).
     * 7. {http://apple.com/ns/ical/}calendar-color
     * 8. {http://apple.com/ns/ical/}calendar-order
     * 9. {urn:ietf:params:xml:ns:caldav}supported-calendar-component-set
     *    (should just be an instance of
     *    Sabre\CalDAV\Property\SupportedCalendarComponentSet, with a bunch of
     *    default components).
     *
     * @param string $principalUri
     *
     * @return array
     */
    public function getSubscriptionsForUser($principalUri)
    {
      $subscriptions = [];
      return $subscriptions;
      
        $fields = array_values($this->subscriptionPropertyMap);
        $fields[] = 'id';
        $fields[] = 'uri';
        $fields[] = 'source';
        $fields[] = 'principaluri';
        $fields[] = 'lastmodified';

        // Making fields a comma-delimited list
        $fields = implode(', ', $fields);
        $stmt = $this->pdo->prepare('SELECT '.$fields.' FROM '.$this->calendarSubscriptionsTableName.' WHERE principaluri = ? ORDER BY calendarorder ASC');
        $stmt->execute([$principalUri]);

        $subscriptions = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $subscription = [
                'id' => $row['id'],
                'uri' => $row['uri'],
                'principaluri' => $row['principaluri'],
                'source' => $row['source'],
                'lastmodified' => $row['lastmodified'],

                '{'.CalDAV\Plugin::NS_CALDAV.'}supported-calendar-component-set' => new CalDAV\Xml\Property\SupportedCalendarComponentSet(['VTODO', 'VEVENT']),
            ];

            foreach ($this->subscriptionPropertyMap as $xmlName => $dbName) {
                if (!is_null($row[$dbName])) {
                    $subscription[$xmlName] = $row[$dbName];
                }
            }

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }

    /**
     * Creates a new subscription for a principal.
     *
     * If the creation was a success, an id must be returned that can be used to reference
     * this subscription in other methods, such as updateSubscription.
     *
     * @param string $principalUri
     * @param string $uri
     *
     * @return mixed
     */
    public function createSubscription($principalUri, $uri, array $properties)
    {
        $fieldNames = [
            'principaluri',
            'uri',
            'source',
            'lastmodified',
        ];

        if (!isset($properties['{http://calendarserver.org/ns/}source'])) {
            throw new Forbidden('The {http://calendarserver.org/ns/}source property is required when creating subscriptions');
        }

        $values = [
            ':principaluri' => $principalUri,
            ':uri' => $uri,
            ':source' => $properties['{http://calendarserver.org/ns/}source']->getHref(),
            ':lastmodified' => time(),
        ];

        foreach ($this->subscriptionPropertyMap as $xmlName => $dbName) {
            if (isset($properties[$xmlName])) {
                $values[':'.$dbName] = $properties[$xmlName];
                $fieldNames[] = $dbName;
            }
        }

        $stmt = $this->pdo->prepare('INSERT INTO '.$this->calendarSubscriptionsTableName.' ('.implode(', ', $fieldNames).') VALUES ('.implode(', ', array_keys($values)).')');
        $stmt->execute($values);

        return $this->pdo->lastInsertId(
            $this->calendarSubscriptionsTableName.'_id_seq'
        );
    }

    /**
     * Updates a subscription.
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
     * @param mixed $subscriptionId
     */
    public function updateSubscription($subscriptionId, PropPatch $propPatch)
    {
        $supportedProperties = array_keys($this->subscriptionPropertyMap);
        $supportedProperties[] = '{http://calendarserver.org/ns/}source';

        $propPatch->handle($supportedProperties, function ($mutations) use ($subscriptionId) {
            $newValues = [];

            foreach ($mutations as $propertyName => $propertyValue) {
                if ('{http://calendarserver.org/ns/}source' === $propertyName) {
                    $newValues['source'] = $propertyValue->getHref();
                } else {
                    $fieldName = $this->subscriptionPropertyMap[$propertyName];
                    $newValues[$fieldName] = $propertyValue;
                }
            }

            // Now we're generating the sql query.
            $valuesSql = [];
            foreach ($newValues as $fieldName => $value) {
                $valuesSql[] = $fieldName.' = ?';
            }

            $stmt = $this->pdo->prepare('UPDATE '.$this->calendarSubscriptionsTableName.' SET '.implode(', ', $valuesSql).', lastmodified = ? WHERE id = ?');
            $newValues['lastmodified'] = time();
            $newValues['id'] = $subscriptionId;
            $stmt->execute(array_values($newValues));

            return true;
        });
    }

    /**
     * Deletes a subscription.
     *
     * @param mixed $subscriptionId
     */
    public function deleteSubscription($subscriptionId)
    {
        $stmt = $this->pdo->prepare('DELETE FROM '.$this->calendarSubscriptionsTableName.' WHERE id = ?');
        $stmt->execute([$subscriptionId]);
    }

    /**
     * Returns a single scheduling object.
     *
     * The returned array should contain the following elements:
     *   * uri - A unique basename for the object. This will be used to
     *           construct a full uri.
     *   * calendardata - The iCalendar object
     *   * lastmodified - The last modification date. Can be an int for a unix
     *                    timestamp, or a PHP DateTime object.
     *   * etag - A unique token that must change if the object changed.
     *   * size - The size of the object, in bytes.
     *
     * @param string $principalUri
     * @param string $objectUri
     *
     * @return array
     */
    public function getSchedulingObject($principalUri, $objectUri)
    {
        $stmt = $this->pdo->prepare('SELECT uri, calendardata, lastmodified, etag, size FROM '.$this->schedulingObjectTableName.' WHERE principaluri = ? AND uri = ?');
        $stmt->execute([$principalUri, $objectUri]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return [
            'uri' => $row['uri'],
            'calendardata' => $row['calendardata'],
            'lastmodified' => $row['lastmodified'],
            'etag' => '"'.$row['etag'].'"',
            'size' => (int) $row['size'],
         ];
    }

    /**
     * Returns all scheduling objects for the inbox collection.
     *
     * These objects should be returned as an array. Every item in the array
     * should follow the same structure as returned from getSchedulingObject.
     *
     * The main difference is that 'calendardata' is optional.
     *
     * @param string $principalUri
     *
     * @return array
     */
    public function getSchedulingObjects($principalUri)
    {
        $stmt = $this->pdo->prepare('SELECT id, calendardata, uri, lastmodified, etag, size FROM '.$this->schedulingObjectTableName.' WHERE principaluri = ?');
        $stmt->execute([$principalUri]);

        $result = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $result[] = [
                'calendardata' => $row['calendardata'],
                'uri' => $row['uri'],
                'lastmodified' => $row['lastmodified'],
                'etag' => '"'.$row['etag'].'"',
                'size' => (int) $row['size'],
            ];
        }

        return $result;
    }

    /**
     * Deletes a scheduling object.
     *
     * @param string $principalUri
     * @param string $objectUri
     */
    public function deleteSchedulingObject($principalUri, $objectUri)
    {
        $stmt = $this->pdo->prepare('DELETE FROM '.$this->schedulingObjectTableName.' WHERE principaluri = ? AND uri = ?');
        $stmt->execute([$principalUri, $objectUri]);
    }

    /**
     * Creates a new scheduling object. This should land in a users' inbox.
     *
     * @param string          $principalUri
     * @param string          $objectUri
     * @param string|resource $objectData
     */
    public function createSchedulingObject($principalUri, $objectUri, $objectData)
    {
        $stmt = $this->pdo->prepare('INSERT INTO '.$this->schedulingObjectTableName.' (principaluri, calendardata, uri, lastmodified, etag, size) VALUES (?, ?, ?, ?, ?, ?)');

        if (is_resource($objectData)) {
            $objectData = stream_get_contents($objectData);
        }

        $stmt->execute([$principalUri, $objectData, $objectUri, time(), md5($objectData), strlen($objectData)]);
    }

    /**
     * Updates the list of shares.
     *
     * @param mixed                           $calendarId
     * @param \Sabre\DAV\Xml\Element\Sharee[] $sharees
     */
    public function updateInvites($calendarId, array $sharees)
    {
        throw new \InvalidArgumentException('Calendar Invites Update not supported');

        return false;

//
//        if (!is_array($calendarId)) {
//            throw new \InvalidArgumentException('The value passed to $calendarId is expected to be an array with a calendarId and an instanceId');
//        }
//        $currentInvites = $this->getInvites($calendarId);
//        list($calendarId, $instanceId) = $calendarId;
//
//        $removeStmt = $this->pdo->prepare('DELETE FROM '.$this->calendarInstancesTableName.' WHERE calendarid = ? AND share_href = ? AND access IN (2,3)');
//        $updateStmt = $this->pdo->prepare('UPDATE '.$this->calendarInstancesTableName.' SET access = ?, share_displayname = ?, share_invitestatus = ? WHERE calendarid = ? AND share_href = ?');
//
//        $insertStmt = $this->pdo->prepare('
//INSERT INTO '.$this->calendarInstancesTableName.'
//    (
//        calendarid,
//        principaluri,
//        access,
//        displayname,
//        uri,
//        description,
//        calendarorder,
//        calendarcolor,
//        timezone,
//        transparent,
//        share_href,
//        share_displayname,
//        share_invitestatus
//    )
//    SELECT
//        ?,
//        ?,
//        ?,
//        displayname,
//        ?,
//        description,
//        calendarorder,
//        calendarcolor,
//        timezone,
//        1,
//        ?,
//        ?,
//        ?
//    FROM '.$this->calendarInstancesTableName.' WHERE id = ?');
//
//        foreach ($sharees as $sharee) {
//            if (\Sabre\DAV\Sharing\Plugin::ACCESS_NOACCESS === $sharee->access) {
//                // if access was set no NOACCESS, it means access for an
//                // existing sharee was removed.
//                $removeStmt->execute([$calendarId, $sharee->href]);
//                continue;
//            }
//
//            if (is_null($sharee->principal)) {
//                // If the server could not determine the principal automatically,
//                // we will mark the invite status as invalid.
//                $sharee->inviteStatus = \Sabre\DAV\Sharing\Plugin::INVITE_INVALID;
//            } else {
//                // Because sabre/dav does not yet have an invitation system,
//                // every invite is automatically accepted for now.
//                $sharee->inviteStatus = \Sabre\DAV\Sharing\Plugin::INVITE_ACCEPTED;
//            }
//
//            foreach ($currentInvites as $oldSharee) {
//                if ($oldSharee->href === $sharee->href) {
//                    // This is an update
//                    $sharee->properties = array_merge(
//                        $oldSharee->properties,
//                        $sharee->properties
//                    );
//                    $updateStmt->execute([
//                        $sharee->access,
//                        isset($sharee->properties['{DAV:}displayname']) ? $sharee->properties['{DAV:}displayname'] : null,
//                        $sharee->inviteStatus ?: $oldSharee->inviteStatus,
//                        $calendarId,
//                        $sharee->href,
//                    ]);
//                    continue 2;
//                }
//            }
//            // If we got here, it means it was a new sharee
//            $insertStmt->execute([
//                $calendarId,
//                $sharee->principal,
//                $sharee->access,
//                \Sabre\DAV\UUIDUtil::getUUID(),
//                $sharee->href,
//                isset($sharee->properties['{DAV:}displayname']) ? $sharee->properties['{DAV:}displayname'] : null,
//                $sharee->inviteStatus ?: \Sabre\DAV\Sharing\Plugin::INVITE_NORESPONSE,
//                $instanceId,
//            ]);
//        }
    }

    /**
     * Returns the list of people whom a calendar is shared with.
     *
     * Every item in the returned list must be a Sharee object with at
     * least the following properties set:
     *   $href
     *   $shareAccess
     *   $inviteStatus
     *
     * and optionally:
     *   $properties
     *
     * @param mixed $calendarId
     *
     * @return \Sabre\DAV\Xml\Element\Sharee[]
     */
    public function getInvites($calendarId)
    {
      $result = [];
      return $result;
      throw new \InvalidArgumentException('Calendar Invites Get not supported');
      return false;
//      
//        if (!is_array($calendarId)) {
//            throw new \InvalidArgumentException('The value passed to getInvites() is expected to be an array with a calendarId and an instanceId');
//        }
//        list($calendarId, $instanceId) = $calendarId;
//
//        $query = <<<SQL
//SELECT
//    principaluri,
//    access,
//    share_href,
//    share_displayname,
//    share_invitestatus
//FROM {$this->calendarInstancesTableName}
//WHERE
//    calendarid = ?
//SQL;
//
//        $stmt = $this->pdo->prepare($query);
//        $stmt->execute([$calendarId]);
//
//        $result = [];
//        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
//            $result[] = new Sharee([
//                'href' => isset($row['share_href']) ? $row['share_href'] : \Sabre\HTTP\encodePath($row['principaluri']),
//                'access' => (int) $row['access'],
//                /// Everyone is always immediately accepted, for now.
//                'inviteStatus' => (int) $row['share_invitestatus'],
//                'properties' => !empty($row['share_displayname'])
//                    ? ['{DAV:}displayname' => $row['share_displayname']]
//                    : [],
//                'principal' => $row['principaluri'],
//            ]);
//        }
//
//        return $result;
    } 

    /**
     * Publishes a calendar.
     *
     * @param mixed $calendarId
     * @param bool  $value
     */
    public function setPublishStatus($calendarId, $value)
    {
        throw new DAV\Exception\NotImplemented('Not implemented');
    }
}
