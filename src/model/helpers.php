<?php

/**
 * Get a model.
 *
 * @param  string $model
 * @param  string $dir
 *
 * @return mixed
 */
function xu_get_model( $model, array $args = [], $dir = 'models' ) {
    if ( method_exists( $model, 'model' ) ) {
        return call_user_func_array( [$model, 'model'], $args );
    }

    $file  = null;
    $names = [
        sprintf( '%s/class-%s.php', $dir, $model ),
        sprintf( '%s/%s.php', $dir, $model )
    ];

    foreach ( $names as $name ) {
        if ( $file = locate_template( $name, true ) ) {
            break;
        }
    }

    if ( empty( $file ) ) {
        return;
    }

    $class_name = xu_get_class_name( $file );
    $reflection = new ReflectionClass( $class_name );

    return $reflection->newInstanceArgs( $args );
}