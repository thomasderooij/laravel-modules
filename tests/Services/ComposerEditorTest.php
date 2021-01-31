<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Thomasderooij\LaravelModules\Contracts\Services\ComposerEditor;
use Thomasderooij\LaravelModules\Tests\Test;

class ComposerEditorTest extends Test
{
    private $filesystem;

    public function setUp(): void
    {
        parent::setUp();

        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->instance('files', $this->filesystem);
    }

    public function testAddNamespaceToAutoload () : void
    {
        // If I have a composer editor
        /** @var ComposerEditor $uut */
        $uut = $this->app->make("module.service.composer_editor");

        // If I have a module root
        $root = "moduleRoot";

        // If I want to add a namespace to the composer.json, I need to fetch the content of the file
        $this->filesystem->shouldReceive("get")->withArgs([base_path("composer.json")])->andReturn($this->getComposerContent());

        // I expect the file to be replaced with an updated version of the composer.json
        $json = null;
        $this->filesystem->shouldReceive("put")->withArgs([
            base_path("composer.json"),
            Mockery::capture($json)
        ])->once();

        $uut->addNamespaceToAutoload($root);
        $this->assertMatchesSnapshot($json);
    }

    public function testRemoveNamespaceFromAutoload () : void
    {
        // If I have a composer editor
        /** @var ComposerEditor $uut */
        $uut = $this->app->make("module.service.composer_editor");

        // If I have a module root
        $root = "moduleRoot";
        $psr4 = ["Thomasderooij\\LaravelModules\\" => "src", "ModuleRoot\\" => $root];

        // If I want to remove a namespace to the composer.json, I need to fetch the content of the file
        $this->filesystem->shouldReceive("get")->withArgs([base_path("composer.json")])->andReturn($this->getComposerContent($psr4));

        // I expect the file to be replaced with an updated version of the composer.json
        $this->filesystem->shouldReceive("put")->withArgs([
            base_path("composer.json"),
            Mockery::capture($json)
        ])->once();

        $uut->removeNamespaceFromAutoload($root);
        $this->assertMatchesSnapshot($json);
    }

    public function testHasNamespaceInAutoload () : void
    {
        // If I have a composer editor
        /** @var ComposerEditor $uut */
        $uut = $this->app->make("module.service.composer_editor");

        // If I have a module root
        $root = "moduleRoot";

        // And I want to check if I have the namespace in my Psr4 autoload
        $this->filesystem->shouldReceive("get")->withArgs([base_path("composer.json")])->andReturn($this->getComposerContent());

        // I should not see my module root in there
        $this->assertFalse($uut->hasNamespaceInAutoload($root));
        // But I should see the src directory in there
        $this->assertTrue($uut->hasNamespaceInAutoload("Thomasderooij\\LaravelModules"));
    }

    private function getComposerContent (array $psr4 = null) : string
    {
        $content = $this->getComposerContentWithoutPsr4();
        if ($psr4 === null) {
            $content["autoload"]["psr-4"] = ["Thomasderooij\\LaravelModules\\" => "src"];
        } else {
            $content["autoload"]["psr-4"] = $psr4;
        }

        return json_encode($content, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);
    }

    private function getComposerContentWithoutPsr4 () : array
    {
        return [
            "name" => "thomasderooij/laravel-modules",
            "description" => "package description",
            "keywords" => ["key", "words"],
            "licence" => "MIT",
            "require" => [
                "package" => "^version",
                "other-packer" => "version.*"
            ],
            "require-dev" => [
                "dev-package" => "1.2.3",
                "helpers" => "1.*"
            ],
            "autoload" => [
            ],
            "autoload-dev" => [
                "psr-4" => ["Thomasderooij\\LaravelModules\\Tests\\" => "tests"]
            ],
            "extra" => [
                "laravel" => ["things"]
            ],
            "prefer-stable" => true
        ];
    }
}
