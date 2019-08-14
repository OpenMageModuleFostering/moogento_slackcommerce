<?php

class Moogento_SlackCommerce_Model_Resource_Setup extends Mage_Core_Model_Resource_Setup
{
    const LENGTH_INDEX_NAME     = 64;
    const LENGTH_FOREIGN_NAME   = 64;

    protected static $_translateMap = array(
        'address'       => 'addr',
        'admin'         => 'adm',
        'attribute'     => 'attr',
        'enterprise'    => 'ent',
        'catalog'       => 'cat',
        'category'      => 'ctgr',
        'customer'      => 'cstr',
        'notification'  => 'ntfc',
        'product'       => 'prd',
        'session'       => 'sess',
        'user'          => 'usr',
        'entity'        => 'entt',
        'datetime'      => 'dtime',
        'decimal'       => 'dec',
        'varchar'       => 'vchr',
        'index'         => 'idx',
        'compare'       => 'cmp',
        'bundle'        => 'bndl',
        'option'        => 'opt',
        'gallery'       => 'glr',
        'media'         => 'mda',
        'value'         => 'val',
        'link'          => 'lnk',
        'title'         => 'ttl',
        'super'         => 'spr',
        'label'         => 'lbl',
        'website'       => 'ws',
        'aggregat'      => 'aggr',
        'minimal'       => 'min',
        'inventory'     => 'inv',
        'status'        => 'sts',
        'agreement'     => 'agrt',
        'layout'        => 'lyt',
        'resource'      => 'res',
        'directory'     => 'dir',
        'downloadable'  => 'dl',
        'element'       => 'elm',
        'fieldset'      => 'fset',
        'checkout'      => 'chkt',
        'newsletter'    => 'nlttr',
        'shipping'      => 'shpp',
        'calculation'   => 'calc',
        'search'        => 'srch',
        'query'         => 'qr'
    );

    /**
     * Convert name using dictionary
     *
     * @param string $name
     * @return string
     */
    public static function shortName($name)
    {
        return strtr($name, self::$_translateMap);
    }

    public function columnExists($table, $column)
    {
        return $this->getConnection()->fetchOne("SHOW COLUMNS FROM `{$this->getTable($table)}` LIKE '$column';");
    }

    public function dropTable($tableName)
    {
        $connection = $this->getConnection();
        $table = $connection->quoteIdentifier($tableName);
        $query = 'DROP TABLE IF EXISTS ' . $table;
        $connection->query($query);
    }

    public function getIdxName($tableName, $fields, $indexType = '')
    {
        $resource = Mage::getSingleton('core/resource');
        if (method_exists($resource, 'getIdxName')) {
            return Mage::getSingleton('core/resource')->getIdxName($tableName, $fields, $indexType);
        } else {
            if (is_array($fields)) {
                $fields = implode('_', $fields);
            }

            switch (strtolower($indexType)) {
                case 'unique':
                    $prefix = 'unq_';
                    $shortPrefix = 'u_';
                    break;
                case 'fulltext':
                    $prefix = 'fti_';
                    $shortPrefix = 'f_';
                    break;
                case 'index':
                default:
                    $prefix = 'idx_';
                    $shortPrefix = 'i_';
            }

            $hash = $tableName . '_' . $fields;

            if (strlen($hash) + strlen($prefix) > self::LENGTH_INDEX_NAME) {
                $short = self::shortName($prefix . $hash);
                if (strlen($short) > self::LENGTH_INDEX_NAME) {
                    $hash = md5($hash);
                    if (strlen($hash) + strlen($shortPrefix) > self::LENGTH_INDEX_NAME) {
                        $hash = $this->_minusSuperfluous($hash, $shortPrefix, self::LENGTH_INDEX_NAME);
                    }
                } else {
                    $hash = $short;
                }
            } else {
                $hash = $prefix . $hash;
            }

            return strtoupper($hash);
        }
    }

    protected function _minusSuperfluous($hash, $prefix, $maxCharacters)
    {
        $diff        = strlen($hash) + strlen($prefix) -  $maxCharacters;
        $superfluous = $diff / 2;
        $odd         = $diff % 2;
        $hash        = substr($hash, $superfluous, - ($superfluous + $odd));
        return $hash;
    }

    public function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        $resource = Mage::getSingleton('core/resource');
        if (method_exists($resource, 'getFkName')) {
            return Mage::getSingleton('core/resource')
                       ->getFkName($priTableName, $priColumnName, $refTableName, $refColumnName);
        } else {
            $priTableName = $this->getTable($priTableName);
            $refTableName = $this->getTable($refTableName);

            $prefix = 'fk_';
            $hash = sprintf('%s_%s_%s_%s', $priTableName, $priColumnName, $refTableName, $refColumnName);
            if (strlen($prefix.$hash) > self::LENGTH_FOREIGN_NAME) {
                $short = self::shortName($prefix.$hash);
                if (strlen($short) > self::LENGTH_FOREIGN_NAME) {
                    $hash = md5($hash);
                    if (strlen($prefix.$hash) > self::LENGTH_FOREIGN_NAME) {
                        $hash = $this->_minusSuperfluous($hash, $prefix, self::LENGTH_FOREIGN_NAME);
                    } else {
                        $hash = $prefix . $hash;
                    }
                } else {
                    $hash = $short;
                }
            } else {
                $hash = $prefix . $hash;
            }

            return strtoupper($hash);
        }
    }

    public function indexExists($table, $column)
    {
        return $this->getConnection()->fetchOne("SHOW INDEX FROM `{$this->getTable($table)}` WHERE Column_name = '$column';");
    }
}