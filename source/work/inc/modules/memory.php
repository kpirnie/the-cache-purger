<?php
/** 
 * MEMORY module
 * 
 * This file contains the memory purge methods
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package The Cache Purger
 * 
*/

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

// check if this trait already exists
if( ! trait_exists( 'MEMORY' ) ) {

    /**
     * Trait FILE
     *
     * This trait contains the memory purge methods
     *
     * @since 7.4
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package The Cache Purger
     *
    */
    trait MEMORY {

        /** 
         * purge_memory_caches
         * 
         * This method attempts to delete the memory based caches
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @return void This method does not return anything
         * 
        */
        private function purge_memory_caches( ) : void {

            // get our options 
            $_opt = KPCPC::get_options( );

            // log it
            KPCPC::write_log( "\tMEMORY PURGE");

            // purge redis
            $this -> purge_redis( $_opt );

            // purge memcached
            $this -> purge_memcached( $_opt );
            
            // purge memcache
            $this -> purge_memcache( $_opt );

        }

        /** 
         * purge_redis
         * 
         * This method attempts to purge the redis servers configured
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_redis( object $_opt ) : void {

            // redis
            $_allow_redis = filter_var( ( $_opt -> remote_redis ) ?? false, FILTER_VALIDATE_BOOLEAN );

            // if we are doing the remote redis
            if( $_allow_redis ) {

                if( class_exists( 'Redis' ) ) {

                    // fire up the redis class
                    $_redis = new Redis( );

                    // get the configured servers
                    $_servers = ( $_opt -> remote_redis_servers ) ?? array( );

                    // make sure we have some
                    if( ! empty( $_servers ) ) {

                        // loop them
                        foreach( $_servers as $_server ) {

                            // try to trap an exception
                            try {

                                // connect
                                $_redis -> connect( $_server['remote_redis_server'], $_server['remote_redis_port'] );

                                // now flush
                                $_redis -> flushAll( );

                                // now close the connection
                                $_redis -> close( );

                            } catch ( Exception $e ) {
                                // do nothing... php will ignore and continue 
                            }

                        }

                    }

                    // clean it up
                    unset( $_redis );

                    // log it
                    KPCPC::write_log( "\t\tRedis Cache" );

                }

            }

        }

        /** 
         * purge_memcache
         * 
         * This method attempts to purge the memcache servers configured
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_memcache( object $_opt ) : void {

            // memcached
            $_allow_memcache = filter_var( ( $_opt -> remote_memcache ) ?? false, FILTER_VALIDATE_BOOLEAN );
            
            // if we are doing the remote memcached
            if( $_allow_memcache ) {

                // make sure the Memcached module is installed for PHP
                if( class_exists( 'Memcache' ) ) {

                    // get the configured memcache servers
                    $_servers = ( $_opt -> remote_memcache_servers ) ?? array( );

                    // make sure this exists
                    if( ! empty( $_servers ) ) {

                        // fire it up
                        $_mc = new Memcache( );

                        // loop them
                        foreach( $_servers as $_server ) {

                            // add the server
                            $_mc -> addServer( $_server['remote_memcache_server'], $_server['remote_memcache_port'] );

                            // now flush it
                            $_mc -> flush( );

                        }

                        // clean it up 
                        unset( $_mc );

                    }

                    // log it
                    KPCPC::write_log( "\t\tMemcache Cache" );

                }

            }

        }

        /** 
         * purge_memcached
         * 
         * This method attempts to purge the memcached servers configured
         * 
         * @since 7.4
         * @access protected
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package The Cache Purger
         * 
         * @param object $_opt The options object
         * 
         * @return void This method does not return anything
         * 
        */
        protected function purge_memcached( object $_opt ) : void {

            // memcached
            $_allow_memcached = filter_var( ( $_opt -> remote_memcached ) ?? false, FILTER_VALIDATE_BOOLEAN );
            
            // if we are doing the remote memcached
            if( $_allow_memcached ) {

                // make sure the Memcached module is installed for PHP
                if( class_exists( 'Memcached' ) ) {

                    // get the configured memcached servers
                    $_servers = ( $_opt -> remote_memcached_servers ) ?? array( );

                    // make sure this exists
                    if( ! empty( $_servers ) ) {

                        // fire it up
                        $_mc = new Memcached( );

                        // loop them
                        foreach( $_servers as $_server ) {

                            // add the server
                            $_mc -> addServer( $_server['remote_memcached_server'], $_server['remote_memcached_port'] );

                            // now flush it
                            $_mc -> flush( );

                        }

                        // clean it up 
                        unset( $_mc );

                    }

                    // log it
                    KPCPC::write_log( "\t\tMemcached Cache" );

                }

            }

        }

    }

}
