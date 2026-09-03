<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Note extends Model
{
    use HasFactory;
    protected $hashed_id;
    protected $fillable = [
        'agenda_id',
        'team_id',
        'type',
        'name',
        'date',
        'place',
        'start_time',
        'end_time',
        'max_execute',
        'issues',
        'link_drive_notulen',
        'file_notulen',
        'status',
        'created_by',
        'updated_by'
    ];

    // public function agenda()
    // {
    //     return $this->belongsTo(Agenda::class);
    // }
    
    public function agendas() : BelongsToMany
    {
        return $this->BelongsToMany(Agenda::class,'note_agenda');
    }
    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function action_items()
    {
        return $this->hasMany(ActionItems::class);
    }

    public static function getMonthlyStatistics()
    {
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;

        $data = self::query()
            ->selectRaw('
                YEAR(date) as year,
                MONTH(date) as month,
                COUNT(*) as total
            ')
            ->whereIn(DB::raw('YEAR(date)'), [$currentYear, $previousYear])
            ->groupBy(DB::raw('YEAR(date)'), DB::raw('MONTH(date)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Buat struktur 12 bulan dengan nilai awal 0
        $statistics = [];

        for ($month = 1; $month <= 12; $month++) {
            $statistics[$month] = [
                'month' => $month,
                'month_name' => Carbon::create()
                    ->month($month)
                    ->translatedFormat('M'),

                $previousYear => 0,
                $currentYear => 0,
            ];
        }

        // Masukkan hasil query
        foreach ($data as $row) {
            $statistics[$row->month][$row->year] = (int) $row->total;
        }

        return [
            'years' => [
                'current' => $currentYear,
                'previous' => $previousYear,
            ],
            'data' => array_values($statistics),
        ];
    }

    /**
     * Hash the ids
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    // protected function id(): Attribute
    // {
    //     return  Attribute::make(
    //         get: fn ($value) => Hashids::encode($value)
    //     );
    // }
    public function getAgendaHashAttribute()
    {
        return  Hashids::encode($this->agenda_id);
    }

    public function getHashedIdAttribute()
    {
        return Hashids::encode($this->id);
    }


}
