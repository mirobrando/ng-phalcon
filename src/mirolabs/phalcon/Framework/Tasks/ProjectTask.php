<?php

namespace mirolabs\phalcon\Framework\Tasks;


use mirolabs\phalcon\Framework\Task;

class ProjectTask extends Task
{

    const MODULES_DIR = 'modules';
    const CONTROLLERS_DIR = 'controllers';
    const CONFIG_DIR = 'config';
    const TRANSLATE_DIR = 'messages';
    const TASKS_DIR = 'tasks';
    const VIEWS_DIR = 'views';
    const SERVICES_DIR = 'services';

    public function listAction($param)
    {
        foreach($param['tasks'] as $name => $param) {
            $this->output()->writeFormat($name, 'info_bold');
            $this->output()->write(' - ');
            $this->output()->writelnFormat($param['description'], 'comment');
        }

    }


    public function createModuleAction($params)
    {
        $projectPath = $params[1];
        $name = '';
        while($name == '') {
            $name = $this->input()->getAnswer('Enter the name of the module');
        }
        $moduleDir = $projectPath . '/' . self::MODULES_DIR . '/' . $name;
        if (file_exists($moduleDir)) {
            $this->output()->writeFormat('module is exists!', 'error');
            return;
        }


        mkdir($moduleDir);
        mkdir($moduleDir . '/'. self::CONTROLLERS_DIR);
        mkdir($moduleDir . '/'. self::TASKS_DIR);
        mkdir($moduleDir . '/'. self::CONFIG_DIR);
        mkdir($moduleDir . '/'. self::TRANSLATE_DIR);
        mkdir($moduleDir . '/'. self::VIEWS_DIR);
        mkdir($moduleDir . '/'. self::SERVICES_DIR);

        file_put_contents($moduleDir . '/'. self::CONFIG_DIR . '/route.yml', "# route.yml\n");
        chmod($moduleDir . '/'. self::CONFIG_DIR . '/route.yml', 0777);

        file_put_contents($moduleDir . '/'. self::CONFIG_DIR . '/services.yml', "# services.yml\n");
        file_put_contents($moduleDir . '/'. self::CONFIG_DIR . '/services.yml', "parameters\n\n", FILE_APPEND);
        file_put_contents($moduleDir . '/'. self::CONFIG_DIR . '/services.yml', "services\n\n", FILE_APPEND);
        file_put_contents($moduleDir . '/'. self::CONFIG_DIR . '/services.yml', "tasks\n\n", FILE_APPEND);
        chmod($moduleDir . '/'. self::CONFIG_DIR . '/services.yml', 0777);

        file_put_contents($moduleDir . '/'. self::TRANSLATE_DIR . '/en.php', "<?php\n\n");
        file_put_contents($moduleDir . '/'. self::TRANSLATE_DIR . '/en.php', "\$messages = [\n];\n", FILE_APPEND);
        chmod($moduleDir . '/'. self::TRANSLATE_DIR . '/en.php', 0777);

        file_put_contents($moduleDir . '/Module.php', "<?php\n\n");
        file_put_contents($moduleDir . '/Module.php', "namespace " . $name . ";\n\n", FILE_APPEND);
        file_put_contents($moduleDir . '/Module.php', "class Module extends \mirolabs\phalcon\Framework\Module\n", FILE_APPEND);
        file_put_contents($moduleDir . '/Module.php', "{\n", FILE_APPEND);
        file_put_contents($moduleDir . '/Module.php', "\tpublic function __construct()\n", FILE_APPEND);
        file_put_contents($moduleDir . '/Module.php', "\t{\n", FILE_APPEND);
        file_put_contents($moduleDir . '/Module.php', "\t\t$this->moduleNamespace =  __NAMESPACE__;\n", FILE_APPEND);
        file_put_contents($moduleDir . '/Module.php', "\t\t$this->modulePath = __DIR__;\n", FILE_APPEND);
        file_put_contents($moduleDir . '/Module.php', "\t}\n", FILE_APPEND);
        file_put_contents($moduleDir . '/Module.php', "}\n", FILE_APPEND);
        chmod($moduleDir . '/Module.php', 0777);

        $answer = $this->input()->getAnswer('Do you want add module to project?', 'y', ['y', 'n']);
        if ($answer == 'y') {
            file_put_contents($projectPath . '/config/modules.yml', "\n" . $name . "\n", FILE_APPEND);
            file_put_contents($projectPath . '/config/modules.yml', "  className: " . $name . "\Module\n", FILE_APPEND);
            file_put_contents($projectPath . '/config/modules.yml', "  path: modules/" . $name . "/Module.php\n", FILE_APPEND);
        }

    }


} 