<div class="modal-dialog modal-lg">
<div class="modal-content">
	<div class="modal-header">
    		<h4 class="modal-title">Generate Purchase Order</h4>
	</div>
	<div class="modal-body">
    		<input type="hidden" id="bomRecordId" value="{$BOM_RECORD_ID}" />
<div class="container-fluid">
	<table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width:70%">Product Name</th>
                <th style="width:30%">Quantity</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$VENDOR_PRODUCTS key=vendorName item=vendorBlock}
                <tr class="table-active vendor-row">
                    <td colspan="2"><b>{$vendorName}</b></td>
                </tr>
                <input type="hidden" class="vendorId" value="{$vendorBlock.vendorid}" />

                {foreach from=$vendorBlock.products item=product}
                    <tr>
                        <td>
                            {$product.productname}
                            <input type="hidden" class="productId" value="{$product.productid}" />
                            <input type="hidden" class="sequenceNo" value="{$product.sequence_no}" />
                        </td>
                        <td>
                            {$product.qty}
                            <input type="hidden" class="productQty" value="{$product.qty}" />
                        </td>
                    </tr>
                {/foreach}
            {/foreach}
        </tbody>
    </table>
	</div>
	</div>
	<div class="modal-footer">
 	   	<button class="btn btn-success" id="generatePOBtn">Generate</button>
    		<button class="btn btn-danger" data-dismiss="modal">Cancel</button>
	</div>
 </div>
</div>

