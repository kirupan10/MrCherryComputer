<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductImportController extends Controller
{
    public function create()
    {
        return view('products.import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $the_file = $request->file('file');

        try{
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range( 2, $row_limit );
            $column_range = range( 'J', $column_limit );
            $startcount = 2;
            $data = array();
            $errors = array();

            foreach ( $row_range as $row ) {
                $buyingPrice = $sheet->getCell( 'F' . $row )->getValue();
                $sellingPrice = $sheet->getCell( 'G' . $row )->getValue();

                // Validate buying price must be less than selling price
                if ($buyingPrice && $sellingPrice && $buyingPrice >= $sellingPrice) {
                    $productName = $sheet->getCell( 'A' . $row )->getValue();
                    $errors[] = "Row {$row}: {$productName} - Buying price ({$buyingPrice}) must be less than selling price ({$sellingPrice})";
                    continue;
                }

                $data[] = [
                    'name'          => $sheet->getCell( 'A' . $row )->getValue(),
                    'category_id'   => $sheet->getCell( 'B' . $row )->getValue(),
                    'unit_id'       => $sheet->getCell( 'C' . $row )->getValue(),
                    'code'          => $sheet->getCell( 'D' . $row )->getValue(),
                    'quantity'      => $sheet->getCell( 'E' . $row )->getValue(),
                    'buying_price'  => $buyingPrice,
                    'selling_price' => $sellingPrice,
                    'product_image' => $sheet->getCell( 'H' . $row )->getValue(),
                ];
                $startcount++;
            }

            // If there are validation errors, return with error message
            if (!empty($errors)) {
                return redirect()
                    ->route('products.index')
                    ->with('error', 'Import failed. Pricing errors found:<br>' . implode('<br>', $errors));
            }

            // Only insert if data is valid
            if (!empty($data)) {
                Product::insert($data);
            } else {
                return redirect()
                    ->route('products.index')
                    ->with('error', 'No valid products to import!');
            }

        } catch (Exception $e) {
            // $error_code = $e->errorInfo[1];
            return redirect()
                ->route('products.index')
                ->with('error', 'There was a problem uploading the data!');
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Data product has been imported!');
    }
}
