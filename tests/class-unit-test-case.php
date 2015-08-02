<?php

namespace Xu\Tests;

use InvalidArgumentException;

class Unit_Test_Case extends \WP_UnitTestCase {

    /**
     * Get random string.
     *
     * @return string
     */
    public function getRandomString() {
        return substr( 'abcdefghijklmnopqrstuvwxyz' , mt_rand( 0 , 25 ) , 1 ) .substr( md5( time( ) ) , 1 );
    }

    /**
     * Test invalid argument in given function.
     *
     * @param array|string $options
     */
    public function invalidArgumentTest( $options = [] ) {
        if ( ! is_array( $options ) && ! is_string( $options ) ) {
            throw new InvalidArgumentException( 'Invalid argument. Must be array or string.' );
        }

        if ( is_string( $options ) ) {
            $options = [
                'fn' => $options
            ];
        }

        if ( ! isset( $options['fn'] ) ) {
            throw new InvalidArgumentException( '`fn` is missing from options array' );
        }

        $defaults = [
            'args' => ['string']
        ];

        $options = array_merge( $defaults, $options );

        $types = [
            'array'  => [],
            'false'  => false,
            'float'  => abs( 1 - mt_rand()/mt_rand() ),
            'int'    => rand(),
            'null'   => null,
            'object' => (object) [],
            'string' => $this->getRandomString(),
            'true'   => true
        ];

        $done = [];

        for ( $i = 0, $l = count( $options['args'] ); $i < $l; $i++ ) {
            $type = $options['args'][$i];

            if ( ! is_array( $type ) ) {
                $type = [$type];
            }

            $temp = $types;

            foreach ( $type as $t ) {
                if ( $t === 'bool' ) {
                    unset( $temp['false'] );
                    unset( $temp['true'] );
                } else if ( isset( $temp[$t] ) ) {
                    unset( $temp[$t] );
                }
            }

            $args = [];

            for ( $j = 0; $j < $l; $j++ ) {
                $arg = $options['args'][$j];
                if ( ! is_array( $arg ) ) {
                    $arg = [$arg];
                }

                if ( in_array( $arg, $done ) ) {
                    $args[] = $types[$arg[0]];
                    $continue = false;
                } else {
                    $args[] = $types[array_rand( $temp )];
                    if ( ! in_array( $type, $done ) ) {
                        $done[] = $type;
                    }
                }
            }

            $this->runInvalidArgumentExceptionTest( $options['fn'], $args );
        }
    }

    /**
     * Run invalid argument test.
     *
     * @param string $fn
     * @param array $args
     */
    public function runInvalidArgumentExceptionTest( $fn, array $args = [] ) {
        try {
            call_user_func_array( $fn, $args );
        } catch( InvalidArgumentException $e ) {
            $this->assertNotEmpty( $e->getMessage() );
        }
    }

}
