  <?php

/**
 * 
 * Setup of Zend_Acl for the project. Zend_Acl give us roles and permissions of users inside the system.
 * @author andregonzaga
 *
 */
class Application_Acl_Setup
{
    /**
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * 
     * Constructor initializing Zend_Acl.
     * 
     * @access public
     * @return null
     */
    public function __construct()
    {
        $this->_acl = new Zend_Acl();
        $this->_initialize();
    }

    /**
     * 
     * Call all of rules of Zend_Acl.
     * 
     * @access protected
     * @return null
     */
    protected function _initialize()
    {
        $this->_setupRoles();
        $this->_setupResources();
        $this->_setupPrivileges();
        $this->_saveAcl();
    }

    /**
     * 
     * Setup roles of system. We have 2 types of users nowadays: guest and user.
     * 
     * @access protected
     * @return null
     */
    protected function _setupRoles()
    {
        $this->_acl->addRole( new Zend_Acl_Role('guest') );
        $this->_acl->addRole( new Zend_Acl_Role('user') );
    }

    /**
     * 
     * Load all of resources (controllers) of system. If it's created another controller
     * we should add here.
     * 
     * @access protected
     * @return null
     */
    protected function _setupResources()
    {
      $this->_acl->addResource( new Zend_Acl_Resource('auth') );
      $this->_acl->addResource( new Zend_Acl_Resource('index') );
      $this->_acl->addResource( new Zend_Acl_Resource('doesntallow') );
      $this->_acl->addResource( new Zend_Acl_Resource('api') );
      $this->_acl->addResource( new Zend_Acl_Resource('dashboard') );
      $this->_acl->addResource( new Zend_Acl_Resource('account') );
      $this->_acl->addResource( new Zend_Acl_Resource('administration') );
      $this->_acl->addResource( new Zend_Acl_Resource('fleet') );
      $this->_acl->addResource( new Zend_Acl_Resource('qco') );
      $this->_acl->addResource( new Zend_Acl_Resource('mco') );
      $this->_acl->addResource( new Zend_Acl_Resource('inspection') );
      $this->_acl->addResource( new Zend_Acl_Resource('finance') );
      $this->_acl->addResource( new Zend_Acl_Resource('mail') );
      $this->_acl->addResource( new Zend_Acl_Resource('scheduling') );
      $this->_acl->addResource( new Zend_Acl_Resource('agendamento') );
      $this->_acl->addResource( new Zend_Acl_Resource('accident') );
      $this->_acl->addResource( new Zend_Acl_Resource('gsection') );
      $this->_acl->addResource( new Zend_Acl_Resource('rate-calculation') );
      $this->_acl->addResource( new Zend_Acl_Resource('indicators') );
    }

    /**
     * 
     * For each action and controller we have to allow a permission of this for each
     * type of user.
     * 
     * @access protected
     * @return null
     */
    protected function _setupPrivileges()
    {
        $this->_acl	->allow( 'guest', 'index', 'index' )
        			->allow( 'guest', 'auth', array('index', 'login') );

        $this->_acl	->allow( 'user', 'index', 'index' )
			        ->allow( 'user', 'auth', array('index', 'login') )
                    ->allow( 'user', 'doesntallow', array('index') )
                    ->allow( 'user', 'api', array(  'consortium-companies', 'return-user', 'return-accidents', 'return-accidents-city',
                                                    'consortium-companies-name') )
                    ->allow( 'user', 'dashboard', array('index') )
                    ->allow( 'user', 'account', array('index','personal','photo','password') )
                    ->allow( 'user', 'administration', array('index',
                                            'user', 'user-new', 'user-edit',
                                            'city', 'city-new', 'city-edit',
                                            'pattern', 'pattern-new', 'pattern-edit',
                                            'chassi', 'chassi-new', 'chassi-edit',
                                            'type', 'type-new', 'type-edit',
                                            'color', 'color-new', 'color-edit',
                                            'body', 'body-new', 'body-edit') )
                    ->allow( 'user', 'fleet', array('index', 'new', 'view', 'report', 'vis', 'edit', 'remove','review', 'refresh',
                                                    'download-file', 'remove-file', 'save-all', 'protocol', 'print-certificate',
                                                    'process', 'accept-review', 'deny-review', 'transfer', 'review-transfer',
                                                    'accept-transfer', 'deny-transfer', 'down', 'ask-crv', 'review-crv',
                                                    'accept-crv', 'deny-crv', 'review-edited', 'report-active', 'delete',
                                                    'report-all','accept-review-edited', 'add-documents') )
                    ->allow( 'user', 'qco', array('index', 'new', 'view', 'edit', 'report', 'vis', 'new-shape',
                                                    'download-file', 'remove-file', 'print', 'remove-qh', 'remove-log',
                                                    'new-qh', 'historic', 'complete-historic','print-historic','calendar','line-calendar',
                                                    'consortium-calendar','cell-calendar', 'export', 'remove-route') )
                    ->allow( 'user', 'mco', array('index', 'new', 'view', 'edit', 'report', 'vis', 'analytics',
                                                    'analytics-result', 'analytics-diff', 'analytics-adjustments', 'analytics-finance',
                                                    'analytics-by-day', 'analytics-finance-month', 'accredit-passenger', 'edit-lost-log', 
                                                    'new-lost-log','report-cell-revenue','report-revenue','delete-lost-log',
                                                    'delete-by-day','lock-day','report-overcrowded','report-travel','report-hour-production',
                                                    'main-new-lost-log') )
                    ->allow( 'user', 'inspection', array('index','vehicle','save-all','protocol', 'save-roulette','view', 'down',
                                                    'accept-down', 'deny-down', 'protocol-block', 'review-crv', 'accept-crv', 'deny-crv') )
                    ->allow( 'user', 'finance', array('index',  'group-fare','group-fare-new', 'group-fare-edit', 'group-fare-remove',
                                                                'group-section', 'group-section-new', 'group-section-edit', 'group-section-remove') )
                    ->allow( 'user', 'mail', array('index', 'inbox', 'outbox', 'new', 'parent', 'parent-out', 'download', 'forward') )
                    ->allow( 'user', 'accident', array('index', 'new', 'edit', 'view', 'delete', 'report', 'vis', 'report-accident-by-date') )
                    ->allow( 'user', 'scheduling', array('index', 'treatment', 'calendar', 'return-events', 'return-all-events', 
                                                    'return-hour', 'return-schedulings', 'hour', 'report', 'delete-hour', 'graphic',
                                                    'return-schedulings-vis', 'report-scheduling', 'remove', 'reschedule','print',
                                                    'print-calendar') )
                    ->allow( 'user', 'agendamento', array('index', 'success') )
                    ->allow( 'user', 'gsection', array('index', 'new', 'view', 'edit') )
                    ->allow( 'user', 'rate-calculation', array('index', 'new', 'view', 'edit', 'coefficient','cost','fix-cost','variable-cost',
                        'fuel','km-production','tread','accessories','lubricant', 'vehicle-depreciation', 'vehicle-remuneration', 'lift-remnuneration', 'equipment-remuneration',
                                                    'warehouse-remuneration', 'eletronic-ticketing', 'operation-crew', 'fixed-expenses', 'social-charges' ) )
                    ->allow( 'user', 'indicators', array('index'));

    }

    /**
     * 
     * Save configuration of Zend_Acl.
     * 
     * @access protected
     * @return null
     */
    protected function _saveAcl()
    {
        $registry = Zend_Registry::getInstance();
        $registry->set('acl', $this->_acl);
    }
}