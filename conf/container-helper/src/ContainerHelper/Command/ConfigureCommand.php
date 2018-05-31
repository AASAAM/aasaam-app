<?php declare(strict_types=1);
/**
 * AASAAM App container configure
 *
 * @category  Application
 * @package   ContainerHelper
 * @author    Muhammad Hussein Fattahizadeh <m@mhf.ir>
 * @copyright 2018 Muhammad Hussein Fattahizadeh <m@mhf.ir>
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @link      https://github.com/AASAAM/aasaam-app/
 */

namespace ContainerHelper\Command;

use ContainerHelper\Command\AbstractCommand;
use ContainerHelper\Profiles;
use ContainerHelper\Template;
use Symfony\Component\Console\Input\InputArgument;

class ConfigureCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('configure')
            ->addArgument('profile', InputArgument::REQUIRED)
            ->setDescription('Configure the container by profile.')
            ->setHelp('Quick way to configure the nginx, php of container');
    }

    protected function executeMe(): void
    {
        define('RANDOM_KEY', substr(preg_replace('/[^a-z0-9]/i', '', base64_encode(hash('sha1', microtime(true) . uniqid()))), 1, 12));
        $profiles = new Profiles();
        $profiles->setProfile($this->input->getArgument('profile'));
        $profiles->apply();
        $swooleMode = $profiles->isSwoole() ? 'active' : 'disable';
        $this->output->writeln(vsprintf('Profile now is <info>%s</info> and swoole mode is <comment>%s</comment>', [
            $profiles->getProfile(),
            $swooleMode,
        ]));
        if ($profiles->getConfig()['phpspx']) {
            $this->output->writeln(vsprintf('SPX http key now is <info>%s</info>', [
                RANDOM_KEY,
            ]));
        }
        $this->output->writeln(vsprintf("Your configuration is now <info>%s</info>", [
            json_encode($profiles->getConfigFile(), JSON_PRETTY_PRINT),
        ]));
        new Template($profiles);
    }
}
