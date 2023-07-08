<?php

namespace Shakurov\Coinbase\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getCharges(array $query = []) Lists all charges.
 * @method static array createCharge(array $params = []) Creates a new charge.
 * @method static array getCharge($chargeId) Retrieves an existing charge by supplying its id or 8 character short-code.
 * @method static array cancelCharge($chargeId) Cancels an existing charge by supplying its id or 8 character short-code.
 * @method static array resolveCharge($chargeId) Resolves an existing, unresolved charge by supplying its id or 8 character short-code.
 * @method static array getCheckouts(array $query = []) Lists all checkouts.
 * @method static array createCheckout(array $params = []) Creates a new checkout.
 * @method static array getCheckout($checkoutId) Retrieves an existing checkout.
 * @method static array updateCheckout($checkoutId, array $params = []) Updates an existing checkout.
 * @method static array deleteCheckout($checkoutId) Deletes an existing checkout.
 * @method static array getInvoices(array $query = []) Lists all invoices.
 * @method static array createInvoice(array $params = []) Creates a new invoice.
 * @method static array getInvoice($invoiceId) Retrieves an existing invoice by supplying its id or 8 character short-code.
 * @method static array voidInvoice($invoiceId) Voids an existing invoice by supplying its id or 8 character short-code.
 * @method static array resolveInvoice($invoiceId) Resolves an existing, unresolved invoice by supplying its id or 8 character short-code.
 * @method static array getEvents(array $query = []) Lists all events.
 * @method static array getEvent($eventId) Retrieves an existing event.
 *
 * @see \Shakurov\Coinbase\Coinbase
 */
class Coinbase extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Shakurov\Coinbase\Coinbase';
    }
}
