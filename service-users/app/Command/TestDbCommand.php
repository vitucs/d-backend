<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use function Hyperf\Support\env;

#[Command]
class TestDbCommand extends HyperfCommand
{
    protected ?string $name = 'test:db';

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->line('--- Diagnóstico Avançado de Configuração ---');

        // Pega o objeto de configuração principal do Hyperf.
        $config = $this->container->get(ConfigInterface::class);

        // 1. Vamos verificar o que o Hyperf pensa que é a sua configuração de banco de dados.
        $this->info('1. Verificando o conteúdo de "config(\'databases\')":');
        var_dump($config->get('databases'));

        $this->line(PHP_EOL . '---');

        // 2. Vamos verificar se o arquivo .env está sendo lido corretamente.
        $this->info('2. Verificando as variáveis de ambiente (env):');
        echo 'DB_HOST: ' . env('DB_HOST', 'NÃO ENCONTRADO') . PHP_EOL;
        echo 'DB_PORT: ' . env('DB_PORT', 'NÃO ENCONTRADO') . PHP_EOL;
        echo 'DB_DATABASE: ' . env('DB_DATABASE', 'NÃO ENCONTRADO') . PHP_EOL;

        $this->line(PHP_EOL . '--- Diagnóstico Concluído ---');
    }
}