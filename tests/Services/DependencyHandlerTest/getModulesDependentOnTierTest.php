<?php

declare(strict_types=1);

namespace DependencyHandlerTest;

use Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest\DependencyHandlerTest;

class getModulesDependentOnTierTest extends DependencyHandlerTest
{
    protected string $method = "getModulesDependentOnTier";
    protected array $dependencies;

    /**
     * We're going to set up a fairly complex dependencies structure here, and write a test for each tier of dependencies,
     *  to see if we're getting returned the modules that we expect to be returned. To visualise it a bit, here's a
     *  diagram illustrating the dependencies
     *
     * So, here's out fictitious business. We have users, companies, and subscriptions, they can publish articles about
     *  stuff. There are adds for stuff in our shop, based on what the article is all about, and the shop has a kickback
     *  fee to our partners, which which we have contracts. All those things get invoiced, and we have our little
     *  bookkeeping software included in here, because why not. We also love making modules for just about everything.
     *
     *  |--------|
     *  | Common |<------------------------|
     *  |--------|                         |
     *     ^                               |
     *  |------|                       |---------|                                             |----------|
     *  | Auth |<----------|           | Finance |                                             | Commerce |
     *  |------|           |           |---------|                                             |----------|
     *      ^              |                ^                                                    ^       ^
     *  |---------|    |------|        |----------|                                    |----------|    |-----------|
     *  | Company |<---| User |        | Invoices |<----------------|                  | Partners |    | Suppliers |
     *  |---------|    |------|        |----------|                 |                  |----------|    |-----------|
     *        ^          ^                   ^                      |                          ^         ^
     *     |--------------|       |-----------------------|    |-------------------|          |-----------|
     *     | Subscription |<------| Subscription invoices |    | Business invoices |--------->| Contracts |
     *     |--------------|       |-----------------------|    |-------------------|          |-----------|
     *             ^                                                 ^                             ^   ^
     *         |--------------|                |------|        |----------|                        |   |
     *         | Publications |                | Shop |<-------| Kickback |------------------------|   |
     *         |--------------|                |------|        |----------|                            |
     *             ^                              ^                                                    |
     *         |---------|             |------|   |                                                    |
     *         | Article |<------------| Adds |---|-----------------------------------------------------
     *         |---------|             |------|
     *
     * Here we go. What a monster.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->dependencies = [
            [$this->upKey => "Common", $this->downKey => "Auth"],
            [$this->upKey => "Common", $this->downKey => "Finance"],
            [$this->upKey => "Auth", $this->downKey => "Company"],
            [$this->upKey => "Auth", $this->downKey => "User"],
            [$this->upKey => "User", $this->downKey => "Subscription"],
            [$this->upKey => "Company", $this->downKey => "Subscription"],
            [$this->upKey => "Company", $this->downKey => "User"],
            [$this->upKey => "Subscription", $this->downKey => "Publications"],
            [$this->upKey => "Subscription", $this->downKey => "Subscription invoices"],
            [$this->upKey => "Publications", $this->downKey => "Article"],
            [$this->upKey => "Article", $this->downKey => "Adds"],
            [$this->upKey => "Finance", $this->downKey => "Invoices"],
            [$this->upKey => "Invoices", $this->downKey => "Subscription invoices"],
            [$this->upKey => "Invoices", $this->downKey => "Business invoices"],
            [$this->upKey => "Business invoices", $this->downKey => "Kickback"],
            [$this->upKey => "Shop", $this->downKey => "Kickback"],
            [$this->upKey => "Shop", $this->downKey => "Adds"],
            [$this->upKey => "Commerce", $this->downKey => "Partners"],
            [$this->upKey => "Commerce", $this->downKey => "Suppliers"],
            [$this->upKey => "Suppliers", $this->downKey => "Contracts"],
            [$this->upKey => "Partners", $this->downKey => "Contracts"],
            [$this->upKey => "Contracts", $this->downKey => "Business invoices"],
            [$this->upKey => "Contracts", $this->downKey => "Adds"],
            [$this->upKey => "Contracts", $this->downKey => "Kickback"],
        ];
    }

    public function testGetModulesDependentOnTier () : void
    {

    }
}
