<?php

use Applistic\Common\KeyValue;

class ApplisticCommonKeyValueTest extends ApplisticCommonTestCase
{
    public static $samples = array(
        'alpha'   => "abcd",
        'integer' => 123,
        'float'   => 23.45,
        'boolean' => true,
    );

    public function testInstanciation()
    {
        $kv = new KeyValue();
        $this->assertTrue(is_a($kv, "Applistic\Common\KeyValue"));
    }

    public function testInstanciationWithArray()
    {
        $kv = new KeyValue(static::$samples);
        $this->assertTrue(is_a($kv, "Applistic\Common\KeyValue"));

        foreach (static::$samples as $key => $value) {
            $this->assertTrue($kv->has($key));
            $this->assertTrue($kv->get($key) == $value);
            $this->assertTrue($kv[$key] == $value);
        }
    }

    public function testIncrement()
    {
        $kv = new KeyValue(['test' => 1]);
        $kv->increase('test');

        $this->assertTrue($kv['test'] == 2);
    }

    public function testIncrementByFloat()
    {
        $kv = new KeyValue(['test' => 1]);
        $kv->increase('test', 1.5);

        $this->assertTrue($kv['test'] == 2.5);
    }

    public function testDecrement()
    {
        $kv = new KeyValue(['test' => 1]);
        $kv->decrease('test');

        $this->assertTrue($kv['test'] == 0);
    }

    public function testDecrementByFloat()
    {
        $kv = new KeyValue(['test' => 1]);
        $kv->decrease('test', 1.5);

        $this->assertTrue($kv['test'] == -0.5);
    }

    public function testJson()
    {
        $kv = new KeyValue([
            'ALPHA' => "abcd",
            'NUM'   => 123,
            'BOOL'  => true,
        ]);

        $expected = '{"ALPHA":"abcd","NUM":123,"BOOL":true}';
        $this->assertEquals($expected, $kv->toJson());
    }

    public function testArray()
    {
        $kv = new KeyValue(static::$samples);
        $this->assertEquals(static::$samples, $kv->toArray());
    }
}