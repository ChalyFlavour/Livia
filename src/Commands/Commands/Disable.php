<?php
/**
 * Livia
 * Copyright 2017 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Livia/blob/master/LICENSE
*/

namespace CharlotteDunois\Livia\Commands\Commands;

return function ($client) {
    return new class($client) extends \CharlotteDunois\Livia\Commands\Command {
        function __construct(\CharlotteDunois\Livia\LiviaClient $client) {
            parent::__construct($client, array(
                'name' => 'disable',
                'aliases' => array('disable-command'),
                'group' => 'commands',
                'description' => 'Disables a command or command group.',
                'details' => 'The argument must be the name/ID (partial or whole) of a command or command group. Only administrators may use this command.',
                'examples' => array('disable utils'),
                'guildOnly' => false,
                'throttling' => array(
                    'usages' => 2,
                    'duration' => 3
                ),
                'userPermissions' => array('ADMINISTRATOR'),
                'args' => array(
                    array(
                        'key' => 'commandOrGroup',
                        'label' => 'command/group',
                        'prompt' => 'Which command or group would you like to disable?',
                        'type' => 'command-or-group'
                    )
                ),
                'guarded' => true
            ));
        }
        
        function run(\CharlotteDunois\Livia\CommandMessage $message, array $args, bool $fromPattern) {
            return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($message, $args) {
                $type = ($args['commandOrGroup'] instanceof \CharlotteDunois\Livia\Commands\CommandGroup ? 'group' : 'command');
                
                if($args['commandOrGroup']->isEnabledIn($message->message->guild) === false) {
                    return $resolve($message->reply('The '.$type.' `'.$args['commandOrGroup']->name.'` is already disabled.'));
                }
                
                if($args['commandOrGroup']->guarded) {
                    return $resolve($message->reply('The '.$type.' `'.$args['commandOrGroup']->name.'` can not be disabled.'));
                }
                
                $args['commandOrGroup']->setEnabledIn($message->message->guild, false);
                $resolve($message->reply('Disabled the '.$type.' `'.$args['commandOrGroup']->name.'`.'));
            }));
        }
    };
};
