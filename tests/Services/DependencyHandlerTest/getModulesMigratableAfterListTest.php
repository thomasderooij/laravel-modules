<?php

declare(strict_types=1);

namespace DependencyHandlerTest;

use Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest\DependencyHandlerTest;

class getModulesMigratableAfterListTest extends DependencyHandlerTest
{
    protected string $method = "getModulesMigratableAfterList";
    protected array $dependencies;
    protected array $modules;

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

        $this->modules = [
            $common = "Common",
            $auth = "Auth",
            $finance = "Finance",
            $company = "Company",
            $user ="User",
            $subscription = "Subscription",
            $publications = "Publications",
            $subInvoices = "Subscription invoices",
            $article = "Article",
            $invoices = "Invoices",
            $adds = "Adds",
            $busInvoices = "Business invoices",
            $kickback = "Kickback",
            $shop = "Shop",
            $commerce = "Commerce",
            $partners = "Partners",
            $suppliers = "Suppliers",
            $contracts = "Contracts",
        ];

        $this->dependencies = [
            [$this->upKey => $common, $this->downKey => $auth],
            [$this->upKey => $common, $this->downKey => $finance],
            [$this->upKey => $auth, $this->downKey => $company],
            [$this->upKey => $auth, $this->downKey => $user],
            [$this->upKey => $user, $this->downKey => $subscription],
            [$this->upKey => $company, $this->downKey => $subscription],
            [$this->upKey => $company, $this->downKey => $user],
            [$this->upKey => $subscription, $this->downKey => $publications],
            [$this->upKey => $subscription, $this->downKey => $subInvoices],
            [$this->upKey => $publications, $this->downKey => $article],
            [$this->upKey => $article, $this->downKey => $adds],
            [$this->upKey => $finance, $this->downKey => $invoices],
            [$this->upKey => $invoices, $this->downKey => $subInvoices],
            [$this->upKey => $invoices, $this->downKey => $busInvoices],
            [$this->upKey => $busInvoices, $this->downKey => $kickback],
            [$this->upKey => $shop, $this->downKey => $kickback],
            [$this->upKey => $shop, $this->downKey => $adds],
            [$this->upKey => $commerce, $this->downKey => $partners],
            [$this->upKey => $commerce, $this->downKey => $suppliers],
            [$this->upKey => $suppliers, $this->downKey => $contracts],
            [$this->upKey => $partners, $this->downKey => $contracts],
            [$this->upKey => $contracts, $this->downKey => $busInvoices],
            [$this->upKey => $contracts, $this->downKey => $adds],
            [$this->upKey => $contracts, $this->downKey => $kickback],
        ];
    }

    /**
     * @group order
     */
    public function testMigratableModulesAfterList () : void
    {
        // If I have a whole bunch of modules with dependencies
        // And I ask which ones are safe to migrate
        // I should get "Common", "Commerce" and "Shop"
        $list = ["vanilla"];
        $expected = ["Common", "Commerce", "Shop"];
        $this->assertSame($this->returnSorted($expected), $this->returnSorted($this->uut->invoke($this->methodHandler, $list, $this->dependencies)));

        // Next, we merge the lists, and try it again
        $list = array_merge($list, $expected);
        // This time, based on the previous modules, we expect "Auth", "Finance", "Partners" and "Suppliers"
        $expected = ["Auth", "Finance", "Partners", "Suppliers"];
        $this->assertSame($this->returnSorted($expected), $this->returnSorted($this->uut->invoke($this->methodHandler, $list, $this->dependencies)));
        // Next, we merge the lists, and try it again
        $list = array_merge($list, $expected);
        // This time, based on the previous modules, we expect "Company", "Invoices", "Contracts"
        $expected = ["Company", "Invoices", "Contracts"];
        $this->assertSame($this->returnSorted($expected), $this->returnSorted($this->uut->invoke($this->methodHandler, $list, $this->dependencies)));
        // Next, we merge the lists, and try it again
        $list = array_merge($list, $expected);
        // This time, based on the previous modules, we expect "User", "Business invoices"
        $expected = ["User", "Business invoices"];
        $this->assertSame($this->returnSorted($expected), $this->returnSorted($this->uut->invoke($this->methodHandler, $list, $this->dependencies)));
        $list = array_merge($list, $expected);
        // This time, based on the previous modules, we expect "Kickback", "Subscription"
        $expected = ["Kickback", "Subscription"];
        $this->assertSame($this->returnSorted($expected), $this->returnSorted($this->uut->invoke($this->methodHandler, $list, $this->dependencies)));
        $list = array_merge($list, $expected);
        // This time, based on the previous modules, we expect "Publications", "Subscription invoices"
        $expected = ["Publications", "Subscription invoices"];
        $this->assertSame($this->returnSorted($expected), $this->returnSorted($this->uut->invoke($this->methodHandler, $list, $this->dependencies)));
        $list = array_merge($list, $expected);
        // This time, based on the previous modules, we expect "Article"
        $expected = ["Article"];
        $this->assertSame($this->returnSorted($expected), $this->returnSorted($this->uut->invoke($this->methodHandler, $list, $this->dependencies)));
        $list = array_merge($list, $expected);
        // This time, based on the previous modules, we expect "Adds"
        $expected = ["Adds"];
        $this->assertSame($this->returnSorted($expected), $this->returnSorted($this->uut->invoke($this->methodHandler, $list, $this->dependencies)));
    }

    private function returnSorted (array $arr) : array
    {
        sort($arr);
        return $arr;
    }
}
