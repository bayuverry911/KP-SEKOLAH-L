<?php

namespace Tests\Unit;

use App\Services\AbsenSiswaService;
use App\Support\Geofence;
use PHPUnit\Framework\TestCase;

class AbsenHelperTest extends TestCase
{
    public function test_format_jarak_meter_dan_km(): void
    {
        $this->assertSame('85 m', Geofence::format(85));
        $this->assertSame('999 m', Geofence::format(999));
        $this->assertSame('1 km', Geofence::format(1000));
        $this->assertSame('1,25 km', Geofence::format(1250));
        $this->assertSame('1,5 km', Geofence::format(1500));
        $this->assertSame('-', Geofence::format(null));
    }

    public function test_normal_kelas_tidak_peka_spasi_dan_kata_kelas(): void
    {
        $this->assertSame('7a', AbsenSiswaService::normalKelas('Kelas 7 A'));
        $this->assertSame('7a', AbsenSiswaService::normalKelas('7A'));
        $this->assertNotSame(AbsenSiswaService::normalKelas('7A'), AbsenSiswaService::normalKelas('17A'));
    }
}
