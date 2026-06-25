# Design QA

- Source visual truth: `C:\Users\Brayan\Downloads\Mokup con publicidad.jpeg`
- Implementation screenshot: `C:\laragon\www\alorangesapp\storage\app\product_catalogs\debug\mockup-product-cards\preview-publicidad-20.png`
- Comparison image: `C:\laragon\www\alorangesapp\storage\app\product_catalogs\debug\mockup-product-cards\comparison-publicidad-20.png`
- Viewport: letter-size PDF product page rendered at 120 DPI
- State: 20-product density, letter B, prices enabled, one advertising image

## Full-view Comparison

The implementation preserves the reference hierarchy: letter header, vertical product cards, colored uppercase title, centered product image, highlighted price, centered reference, and a large advertising block aligned to the upper-right.

## Focused Comparison

A separate crop was not required because the rendered full-page screenshot keeps card typography, image scaling, price, and reference text readable at the comparison resolution.

## Findings

- No actionable P0, P1, or P2 differences remain.
- Advertising occupies the equivalent of four product slots: two columns by two rows.
- A page configured for 20 products displays up to 16 products when advertising is present; a page configured for 12 displays up to 8. Subsequent pages return to their full capacity.
- The 12-product option remains available with 3 columns by 4 rows and larger cards.
- Card colors continue to use the configured palette for each letter instead of hard-coding the reference green.
- Product images use their original proportions and the configured placeholder when no image is available.

## Patches Made

- Replaced the horizontal image/content split with a vertical product card.
- Removed the repeated product name from the card body.
- Centered the image, price, and reference.
- Added independent proportions for 12-product and 20-product layouts.
- Moved advertising from a standalone full page into the product grid.
- Positioned advertising on the right and allowed one advertising block per successive product page for the selected letter.
- Kept the implementation compatible with mPDF table rendering.

final result: passed
