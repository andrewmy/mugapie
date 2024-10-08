<?php

declare(strict_types=1);

namespace App\Domain\Model\Order;

use Assert\Assert;

final class ShippingAddress
{
    public const DOMESTIC_COUNTRY = 'US';

    public const DOMESTIC_REGIONS = [
        'AA',
        'AE',
        'AP',
        'AK',
        'AL',
        'AR',
        'AZ',
        'CA',
        'CO',
        'CT',
        'DC',
        'DE',
        'FL',
        'GA',
        'GU',
        'HI',
        'IA',
        'ID',
        'IL',
        'IN',
        'KS',
        'KY',
        'LA',
        'MA',
        'MD',
        'ME',
        'MI',
        'MN',
        'MO',
        'MS',
        'MT',
        'NC',
        'ND',
        'NE',
        'NH',
        'NJ',
        'NM',
        'NV',
        'NY',
        'OH',
        'OK',
        'OR',
        'PA',
        'PR',
        'RI',
        'SC',
        'SD',
        'TN',
        'TX',
        'UT',
        'VA',
        'VI',
        'VT',
        'WA',
        'WI',
        'WV',
        'WY',
    ];

    private string $countryCode;

    private ?string $region;

    private string $city;

    private ?string $street;

    private ?string $address;

    private ?string $zip;

    private string $phone;

    private string $fullName;

    private function __construct()
    {
        // nothing here
    }

    public static function createDomestic(
        string $region,
        string $city,
        string $street,
        string $zip,
        string $phone,
        string $fullName
    ): self {
        $obj              = new self();
        $obj->countryCode = self::DOMESTIC_COUNTRY;
        $obj->region      = $region;
        $obj->city        = $city;
        $obj->street      = $street;
        $obj->zip         = $zip;
        $obj->phone       = $phone;
        $obj->fullName    = $fullName;

        return $obj;
    }

    public static function createInternational(
        string $countryCode,
        ?string $region,
        string $city,
        string $address,
        ?string $zip,
        string $phone,
        string $fullName
    ): self {
        Assert::that($countryCode)->notSame(self::DOMESTIC_COUNTRY);

        $obj              = new self();
        $obj->countryCode = $countryCode;
        $obj->region      = $region;
        $obj->city        = $city;
        $obj->address     = $address;
        $obj->zip         = $zip;
        $obj->phone       = $phone;
        $obj->fullName    = $fullName;

        return $obj;
    }

    public static function isDomesticCountry(?string $countryCode): bool
    {
        return $countryCode === self::DOMESTIC_COUNTRY;
    }

    public function isDomestic(): bool
    {
        return self::isDomesticCountry($this->countryCode());
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function region(): ?string
    {
        return $this->region;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function street(): ?string
    {
        return $this->street;
    }

    public function address(): ?string
    {
        return $this->address;
    }

    public function zip(): ?string
    {
        return $this->zip;
    }

    public function phone(): string
    {
        return $this->phone;
    }

    public function fullName(): string
    {
        return $this->fullName;
    }
}
