<?php

namespace Brunty\LaravelBehatFixtures;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\ScenarioScope;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Due to using laracasts/behat-laravel-extension we have to extend MinkContext
 * Otherwise we get errors as things like Facades don't work
 */
class FixtureContext extends MinkContext
{

    /**
     * @var bool
     */
    private $tagLoaded = false;

    /**
     * @var array
     */
    private $tags = [];

    /**
     * @var string
     */
    private $fixtureDirectory;

    public function __construct($fixtureDirectory)
    {
        $this->fixtureDirectory = $fixtureDirectory;
    }

    /**
     * @BeforeScenario @db-refresh
     */
    public function refreshMigrations()
    {
        Artisan::call('migrate:refresh');
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $event
     */
    public function loadFixtures(BeforeScenarioScope $event)
    {
        $this->storeTags($event);
        $files = $this->getContentOfTags('fixture');
        $this->loadFiles($files);
    }

    /**
     * @param BeforeScenarioScope $event
     */
    private function storeTags(BeforeScenarioScope $event)
    {
        if ($this->tagLoaded === false) {
            if ($event instanceof ScenarioScope) {
                $feature = $event->getFeature();
                $scenario = $event->getScenario();
                if ($feature !== null) {
                    $this->tags = array_merge($this->tags, $feature->getTags());
                }
                if ($scenario !== null) {
                    $this->tags = array_merge($this->tags, $scenario->getTags());
                }
            }
            $this->tagLoaded = true;
        }
    }

    /**
     * @param $name
     *
     * @return array
     */
    private function getContentOfTags($name)
    {
        $tagContent = [];
        foreach ($this->tags as $tag) {
            $matches = [];
            if (preg_match(sprintf('/^%s\((.*)\)$/', $name), $tag, $matches)) {
                $tagContent[] = end($matches);
            }
        }

        return $tagContent;
    }

    private function loadFiles(array $files)
    {
        $directory = rtrim($this->fixtureDirectory, '/');

        foreach($files as $file) {
            include sprintf("%s/%s.php", $directory, $file);
        }
    }
}
