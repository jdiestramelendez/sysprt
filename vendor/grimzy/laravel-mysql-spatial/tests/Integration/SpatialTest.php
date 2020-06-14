<?php

use Grimzy\LaravelMysqlSpatial\SpatialServiceProvider;
use Grimzy\LaravelMysqlSpatial\Types\GeometryCollection;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\MultiPoint;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Illuminate\Filesystem\Filesystem;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

class SpatialTest extends BaseTestCase
{
    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../vendor/laravel/laravel/bootstrap/app.php';
        $app->register(SpatialServiceProvider::class);

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $app['config']->set('database.connections.mysql.database', 'spatial_test');
        $app['config']->set('database.connections.mysql.username', 'root');
        $app['config']->set('database.connections.mysql.password', '');
        $app['config']->set('database.connections.mysql.modes', [
            'ONLY_FULL_GROUP_BY',
            'STRICT_TRANS_TABLES',
            'NO_ZERO_IN_DATE',
            'NO_ZERO_DATE',
            'ERROR_FOR_DIVISION_BY_ZERO',
            'NO_ENGINE_SUBSTITUTION',
        ]);

        return $app;
    }

    /**
     * Setup DB before each test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->onMigrations(function ($migrationClass) {
            (new $migrationClass())->up();
        });
    }

    public function tearDown()
    {
        $this->onMigrations(function ($migrationClass) {
            (new $migrationClass())->down();
        }, true);

        parent::tearDown();
    }

    protected function assertDatabaseHas($table, array $data, $connection = null)
    {
        if (method_exists($this, 'seeInDatabase')) {
            $this->seeInDatabase($table, $data, $connection);
        } else {
            parent::assertDatabaseHas($table, $data, $connection);
        }
    }

    protected function assertException($exceptionName)
    {
        if (method_exists(parent::class, 'expectException')) {
            parent::expectException($exceptionName);
        } else {
            $this->setExpectedException($exceptionName);
        }
    }

    private function onMigrations(\Closure $closure, $reverse_sort = false)
    {
        $fileSystem = new Filesystem();
        $classFinder = new Tools\ClassFinder();

        $migrations = $fileSystem->files(__DIR__.'/Migrations');
        $reverse_sort ? rsort($migrations, SORT_STRING) : sort($migrations, SORT_STRING);

        foreach ($migrations as $file) {
            $fileSystem->requireOnce($file);
            $migrationClass = $classFinder->findClass($file);

            $closure($migrationClass);
        }
    }

    public function testSpatialFieldsNotDefinedException()
    {
        $geo = new NoSpatialFieldsModel();
        $geo->geometry = new Point(1, 2);
        $geo->save();

        $this->assertException(\Grimzy\LaravelMysqlSpatial\Exceptions\SpatialFieldsNotDefinedException::class);
        NoSpatialFieldsModel::all();
    }

    public function testInsertPoint()
    {
        $geo = new GeometryModel();
        $geo->location = new Point(1, 2);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertLineString()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);
        $geo->line = new LineString([new Point(1, 1), new Point(2, 2)]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertPolygon()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);
        $geo->shape = Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))');
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertMultiPoint()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);
        $geo->multi_locations = new MultiPoint([new Point(1, 1), new Point(2, 2)]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertMultiPolygon()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);

        $geo->multi_shapes = new MultiPolygon([
            Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))'),
            Polygon::fromWKT('POLYGON((0 0,0 5,5 5,5 0,0 0))'),
        ]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertGeometryCollection()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);

        $geo->multi_geometries = new GeometryCollection([
            Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))'),
            Polygon::fromWKT('POLYGON((0 0,0 5,5 5,5 0,0 0))'),
            new Point(0, 0),
        ]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertEmptyGeometryCollection()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);

        $geo->multi_geometries = new GeometryCollection([]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);

        $geo2 = GeometryModel::find($geo->id);
        $this->assertNull($geo2->multi_geometries); // MySQL 5.6 saves null instead of empty GeometryCollection
    }

    public function testUpdate()
    {
        $geo = new GeometryModel();
        $geo->location = new Point(1, 2);
        $geo->save();

        $to_update = GeometryModel::all()->first();
        $to_update->location = new Point(2, 3);
        $to_update->save();

        $this->assertDatabaseHas('geometry', ['id' => $to_update->id]);

        $all = GeometryModel::all();
        $this->assertCount(1, $all);

        $updated = $all->first();
        $this->assertInstanceOf(Point::class, $updated->location);
        $this->assertEquals(2, $updated->location->getLat());
        $this->assertEquals(3, $updated->location->getLng());
    }

    public function testDistance()
    {
        $loc1 = new GeometryModel();
        $loc1->location = new Point(1, 1);
        $loc1->save();

        $loc2 = new GeometryModel();
        $loc2->location = new Point(2, 2); // Distance from loc1: 1.4142135623731
        $loc2->save();

        $loc3 = new GeometryModel();
        $loc3->location = new Point(3, 3); // Distance from loc1: 2.8284271247462
        $loc3->save();

        $a = GeometryModel::distance('location', $loc1->location, 2)->get();
        $this->assertCount(2, $a);
        $this->assertTrue($a->contains('location', $loc1->location));
        $this->assertTrue($a->contains('location', $loc2->location));
        $this->assertFalse($a->contains('location', $loc3->location));

        // Excluding self
        $b = GeometryModel::distance('location', $loc1->location, 2, true)->get();
        $this->assertCount(1, $b);
        $this->assertFalse($b->contains('location', $loc1->location));
        $this->assertTrue($b->contains('location', $loc2->location));
        $this->assertFalse($b->contains('location', $loc3->location));

        $c = GeometryModel::distance('location', $loc1->location, 1)->get();
        $this->assertCount(1, $c);
        $this->assertTrue($c->contains('location', $loc1->location));
        $this->assertFalse($c->contains('location', $loc2->location));
        $this->assertFalse($c->contains('location', $loc3->location));
    }

    public function testDistanceValue()
    {
        $loc1 = new GeometryModel();
        $loc1->location = new Point(1, 1);
        $loc1->save();

        $loc2 = new GeometryModel();
        $loc2->location = new Point(2, 2); // Distance from loc1: 1.4142135623731
        $loc2->save();

        $a = GeometryModel::distanceValue('location', $loc1->location)->get();
        $this->assertCount(2, $a);
        $this->assertEquals(0, $a[0]->distance);
        $this->assertEquals(1.4142135623, $a[1]->distance); // PHP floats' 11th+ digits don't matter
    }
}
