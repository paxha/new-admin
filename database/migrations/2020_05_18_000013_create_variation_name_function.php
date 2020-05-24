<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateVariationNameFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('drop function if exists variation_name;');
        DB::unprepared('
        create function variation_name(variation_id bigint unsigned)
            returns varchar(255)
            deterministic
        begin
            declare variation_name varchar(255);

            select concat(b.name, \' \', p.name, \' \', coalesce(group_concat(concat(a.name, \':\', av.value, if(av.unit_id, u.name, \'\')) separator \', \'),\'\'))
            into variation_name
            from variations
                     left join products p on variations.product_id = p.id
                     left join brands b on p.brand_id = b.id
                     left join attribute_variation av on variations.id = av.variation_id
                     left join attributes a on av.attribute_id = a.id
                     left join units u on av.unit_id = u.id
            where variations.id = variation_id group by variations.id;

            return variation_name;
        end;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('drop function if exists product_variation_name;');
    }
}
