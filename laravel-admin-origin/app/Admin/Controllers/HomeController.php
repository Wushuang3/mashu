<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return redirect('/admin/teachers');

        /*
        return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
        */
    }

    public function word()
    {
        $word = new PhpWord();
        $word->setDefaultFontName('仿宋');
        $word->setDefaultFontSize(16);
        $section = $word->createSection();
        //默认样式
        $section->addText('Hello PHP!');
        $section->addTextBreak();//换行符
        $section->addText(
          'Hello world',
          [
              'name' => '宋体',
              'size' => 20,
              'bold' => true
          ]
        );
        $section->addTextBreak(5); // 多个换行符
        $myStyle = 'myStyle';
        $word->addFontStyle(
            $myStyle,
            [
                'name' => 'Verdana',
                'size' => 12,
                'color' => '1BFF32',
                'bold' => true,
                'spaceAfter' => 20
            ]
        );

        $styleTable = [
            'borderColor' => '006699',
            'borderSize' => 6,
            'cellMargin' => 50
        ];
        $styleFirstRow = ['bgColor' => '66BBFF']; // 第一行样式
        $word->addTableStyle('myTable', $styleTable, $styleFirstRow);

        $table = $section->addTable('myTable');
        $table->addRow(400); // 行高
        $table->addCell(2000)->addText('学号');
        $table->addCell(2000)->addText('姓名');
        $table->addCell(2000)->addText('专业');
        $table->addRow(400);
        $table->addCell(2000)->addText('1');
        $table->addCell(2000)->addText('张三');
        $table->addCell(2000)->addText('a');
        $table->addRow(400);
        $table->addCell(2000)->addText('2');
        $table->addCell(2000)->addText('李四');
        $imageStyle = ['width' => 100, 'height' => 100, 'align' => 'center'];
        $table->addCell(2000)->addImage('https://ss0.bdstatic.com/94oJfD_bAAcT8t7mm9GUKT-xh_/timg?image&quality=100&size=b4000_4000&sec=1586523191&di=8299ec2cc2d5e22a145d97fab9bb45ea&src=http://a3.att.hudong.com/14/75/01300000164186121366756803686.jpg', $imageStyle);

        $section->addText('Hello laravel', $myStyle);
        $section->addPageBreak(); // 分隔符
        $writer = IOFactory::createWriter($word, 'Word2007');
        $writer->save('hessl.docx');
        return response()->download('hessl.docx');

    }
}
