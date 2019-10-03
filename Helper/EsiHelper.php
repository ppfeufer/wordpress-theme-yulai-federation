<?php

/**
 * Talking to the ESI API ...
 */

namespace WordPress\Themes\YulaiFederation\Helper;

use \WordPress\EsiClient\Repository\AllianceRepository;
use \WordPress\EsiClient\Repository\CharacterRepository;
use \WordPress\EsiClient\Repository\CorporationRepository;
use \WordPress\EsiClient\Repository\UniverseRepository;

\defined('ABSPATH') or die();

class EsiHelper {
    /**
     * Image Server URL
     *
     * @var string
     */
    private $imageserverUrl = null;

    /**
     * Image Server Endpoints
     *
     * @var array
     */
    private $imageserverEndpoints = null;

    /**
     * instance
     *
     * static variable to keep the current (and only!) instance of this class
     *
     * @var Singleton
     */
    protected static $instance = null;

    /**
     * allianceApi
     *
     * @var AllianceRepository
     */
    private $allianceApi = null;

    /**
     * corporationApi
     *
     * @var CorporationRepository
     */
    private $corporationApi = null;

    /**
     * characterApi
     *
     * @var CharacterRepository
     */
    private $characterApi = null;

    /**
     * universeApi
     *
     * @var UniverseRepository
     */
    private $universeApi = null;

    /**
     * Returning the instance
     *
     * @return \WordPress\Themes\YulaiFederation\Helper\EsiHelper
     */
    public static function getInstance() {
        if(null === self::$instance) {
            self::$instance = new self;
        } // END if(null === self::$instance)

        return self::$instance;
    }

    /**
     * clone
     *
     * no cloning allowed
     */
    protected function __clone() {
        ;
    }

    /**
     * constructor
     *
     * no external instanciation allowed
     */
    protected function __construct() {
        $this->imageserverUrl = 'https://imageserver.eveonline.com/';

        /**
         * ESI API Client
         */
        $this->allianceApi = new AllianceRepository;
        $this->corporationApi = new CorporationRepository;
        $this->characterApi = new CharacterRepository;
        $this->universeApi = new UniverseRepository;

        /**
         * Assigning Imagesever Endpoints
         */
        $this->imageserverEndpoints = [
            'alliance' => 'Alliance/',
            'corporation' => 'Corporation/',
            'character' => 'Character/',
            'item' => 'Type/',
            'inventory' => 'InventoryType/' // Ships and all the other stuff
        ];
    }

    /**
     * Get the image sever URL
     *
     * @return string
     */
    public function getImageServerUrl() {
        return $this->imageserverUrl;
    }

    /**
     * Getting an image server endpoint
     *
     * @param string $group
     * @return string
     */
    public function getImageServerEndpoint($group) {
        return $this->getImageServerUrl() . $this->imageserverEndpoints[$group];
    }

    /**
     * Get the IDs to an array of names
     *
     * @param array $names
     * @param string $type
     * @return type
     */
    public function getIdFromName(array $names, string $type) {
        $returnData = null;

        /* @var $esiData \WordPress\EsiClient\Model\Universe\Ids */
        $esiData = $this->universeApi->universeIds(\array_values($names));

        if(\is_a($esiData, '\WordPress\EsiClient\Model\Universe\Ids')) {
            switch($type) {
                case 'agents':
                    $returnData = $esiData->getAgents();
                    break;

                case 'alliances':
                    $returnData = $esiData->getAlliances();
                    break;

                case 'constellations':
                    $returnData = $esiData->getConstellations();
                    break;

                case 'characters':
                    $returnData = $esiData->getCharacters();
                    break;

                case 'corporations':
                    $returnData = $esiData->getCorporations();
                    break;

                case 'factions':
                    $returnData = $esiData->getFactions();
                    break;

                case 'inventoryTypes':
                    $returnData = $esiData->getInventoryTypes();
                    break;

                case 'regions':
                    $returnData = $esiData->getRegions();
                    break;

                case 'stations':
                    $returnData = $esiData->getStations();
                    break;

                case 'systems':
                    $returnData = $esiData->getSystems();
                    break;
            }
        }

        return $returnData;
    }

    /**
     * Get a pilots avatar by his name
     *
     * @param string $characterName
     * @param boolean $imageOnly
     * @param int $size
     * @param string $newWidth
     * @param string $newHeight
     * @return string
     */
    public function getCharacterImageByName($characterName, $imageOnly = true, $size = 128, $newWidth = null, $newHeight = null) {
        $returnData = null;

        $characterData = $this->getIdFromName([\trim($characterName)], 'characters');
        $characterID = (!\is_null($characterData)) ? $characterData['0']->getId() : null;

        // If we actually have a characterID
        if(!\is_null($characterID)) {
            $imageName = $characterID . '_' . $size. '.jpg';
            $imagePath = $this->imageserverUrl . $this->imageserverEndpoints['character'] . $imageName;

            if($imageOnly === true) {
                return $imagePath;
            }

            if(!\is_null($newWidth)) {
                $newWidth = ' width="' . $newWidth . '"';
            }

            if(!\is_null($newHeight)) {
                $newHeight = ' height="' . $newHeight . '"';
            }

            $returnData = '<img src="' . $imagePath . '" class="eve-character-image eve-character-id-' . $characterID . '" alt="' . $characterName . '">';
        }

        return $returnData;
    }

    /**
     * Get a corp or alliance logo by it's entity name
     *
     * @param string $entityName
     * @param string $entityType corporation/alliance
     * @param boolean $imageOnly
     * @param int $size
     * @return string
     */
    public function getEntityLogoByName($entityName, $entityType, $imageOnly = true, $size = 128) {
        $returnData = null;

        $eveEntityData = $this->getIdFromName([\trim($entityName)], $entityType . 's');
        $eveID = (!\is_null($eveEntityData)) ? $eveEntityData['0']->getId() : null;

        if(!\is_null($eveID)) {
            $imageName = $eveID . '_' . $size . '.png';
            $imagePath = $this->imageserverUrl . $this->imageserverEndpoints[$entityType] . $imageName;

            if($imageOnly === true) {
                return $imagePath;
            }

            $returnData = '<img src="' . $imagePath . '" class="eve-' . $entityType . '-logo">';
        }

        return $returnData;
    }
}
