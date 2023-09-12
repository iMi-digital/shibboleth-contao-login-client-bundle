<?php

declare(strict_types=1);

/*
 * This file is part of Shibboleth Contao Login Client Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/swiss-alpine-club-contao-login-client-bundle
 */

namespace iMi\ContaoShibbolethLoginClientBundle\Security\Auth;

class AuthUser
{
    public function __construct(
        protected array $arrData
    ) {
    }

    /**
     * For testing purposes it is useful
     * to override the user data with dummy data.
     */
    public function overrideData($arrData): void
    {
        $this->arrData = $arrData;
    }

    /**
     * Returns the identifier of the authorized resource owner.
     */
    public function getId(): string
    {
        return $this->arrData['uid'];
    }

    /**
     * Returns the raw resource owner response.
     */
    public function toArray(): array
    {
        return $this->arrData;
    }

    public function getSalutation(): string
    {
        return '';
    }

    public function getLastName(): string
    {
        return $this->arrData['sn'] ?? '';
    }

    public function getFirstName(): string
    {
        return $this->arrData['cn'] ?? '';
    }

    /**
     * Returns the full name (e.g Fritz Muster).
     */
    public function getFullName(): string
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    public function getStreet(): string
    {
        return '';
    }

    public function getPostal(): string
    {
        return '';
    }

    public function getCity(): string
    {
        return '';
    }

    public function getCountryCode(): string
    {
        return strtolower($this->arrData['land'] ?? '');
    }

    public function getDateOfBirth(): string
    {
        return '';
    }

    public function getEmail(): string
    {
        return $this->arrData['mail'] ?? '';
    }

    public function getPhoneMobile(): string
    {
        return '';
    }

    public function getPhonePrivate(): string
    {
        return '';
    }

    public function getPhoneBusiness(): string
    {
        return '';
    }

    public function getRolesAsString(): string
    {
        return $this->arrData['unscoped-affiliation'] ?? '';
    }

    public function getRolesAsArray(): array
    {
        return array_map(static fn ($item) => trim($item, '"'), explode(',', $this->arrData['unscoped-affiliation']));
    }

    public function getDummyResourceOwnerData(bool $isMember): array
    {
        if (true === $isMember) {
            return [
                'telefonmobil' => '079 999 99 99',
                'sub' => '0e592343a-2122-11e8-91a0-00505684a4ad',
                'telefong' => '041 984 13 50',
                'familienname' => 'Messner',
                'strasse' => 'Schloss Juval',
                'vorname' => 'Reinhold',
                'Roles' => 'NAV_BULLETIN,NAV_EINZEL_00999998,NAV_D,NAV_STAMMSEKTION_S00004250,NAV_EINZEL_S00004250,NAV_EINZEL_S00004251,NAV_S00004250,NAV_F1540,NAV_BULLETIN_S00004250,Internal/everyone,NAV_NAVISION,NAV_EINZEL,NAV_MITGLIED_S00004250,NAV_HERR,NAV_F1004V,NAV_F1004V_S00004250,NAV_BULLETIN_S00004250_PAPIER',
                'contact_number' => '999998',
                'ort' => 'Vinschgau IT',
                'geburtsdatum' => '25.05.1976',
                'anredecode' => 'HERR',
                'name' => 'Messner Reinhold',
                'land' => 'IT',
                'kanton' => 'ST',
                'korrespondenzsprache' => 'D',
                'telefonp' => '099 999 99 99',
                'email' => 'r.messner@matterhorn-kiosk.ch',
                'plz' => '6208',
            ];
        }

        // Non member
        return [
            'telefonmobil' => '079 999 99 99',
            'sub' => '0e59877743a-2122-11e8-91a0-00505684a4ad',
            'telefong' => '041 984 13 50',
            'familienname' => 'Rébuffat',
            'strasse' => 'Rue de chamois',
            'vorname' => 'Gaston',
            'Roles' => 'NAV_BULLETIN,NAV_EINZEL_00999999,NAV_D,NAV_STAMMSEKTION_S00009999,NAV_EINZEL_S00009999,NAV_EINZEL_S00009999,NAV_S00009999,NAV_F1540,NAV_BULLETIN_S00009999,Internal/everyone,NAV_NAVISION,NAV_EINZEL,NAV_MITGLIED_S00009999,NAV_HERR,NAV_F1004V,NAV_F1004V_S00009999,NAV_BULLETIN_S00009999_PAPIER',
            'contact_number' => '999999',
            'ort' => 'Chamonix FR',
            'geburtsdatum' => '25.05.1976',
            'anredecode' => 'HERR',
            'name' => 'Gaston Rébuffat',
            'land' => 'IT',
            'kanton' => 'ST',
            'korrespondenzsprache' => 'D',
            'telefonp' => '099 999 99 99',
            'email' => 'g.rebuffat@chamonix.fr',
            'plz' => '6208',
        ];
    }
}
