<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ely
 * Date: 11/18/2015
 * Time: 12:26 PM
 */

namespace Edm\Form;

use Edm\Service\TermTaxonomyServiceAware,
    Edm\Service\TermTaxonomyServiceAwareTrait,
    Edm\Form\TermTaxonomyOptionsTrait;

class TermTaxonomyServiceAwareForm extends AbstractForm
    implements TermTaxonomyServiceAware
{
    use TermTaxonomyServiceAwareTrait,
        TermTaxonomyOptionsTrait;
}
