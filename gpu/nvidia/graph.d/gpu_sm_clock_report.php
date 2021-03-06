<?php

/* Pass in by reference! */
function graph_gpu_sm_clock_report ( &$rrdtool_graph ) {

    global $context,
           $hostname,
           $mem_shared_color,
           $mem_cached_color,
           $mem_buffered_color,
           $mem_swapped_color,
           $mem_used_color,
           $cpu_num_color,
           $range,
           $rrd_dir,
           $size,
           $strip_domainname;

    if ($strip_domainname) {
       $hostname = strip_domainname($hostname);
    }
    $dIndex = $rrdtool_graph["arguments"]["dindex"];
    $title = 'GPU'.$dIndex.' SM Clock';
    $rrdtool_graph['title'] = $title;
    $rrdtool_graph['lower-limit']    = '0';
    $rrdtool_graph['upper-limit']    = '1000.0';
    $rrdtool_graph['vertical-label'] = 'MHz';
    $rrdtool_graph['extras']         = '--rigid --base 1024';
    
    //for max line dot style
    include_once __DIR__."/gpu_common.php"; 
    list($range, $mod) =  calculate_mod_range($range);

    $series = "DEF:'gpu_sm_clock'='${rrd_dir}/gpu".$dIndex."_sm_clock_report.rrd':'sum':AVERAGE "
             ."DEF:gpu_sm_max_speed=${rrd_dir}/gpu".$dIndex."_max_sm_clock.rrd:sum:AVERAGE "
             ."DEF:temp=${rrd_dir}/gpu".$dIndex."_max_sm_clock.rrd:sum:AVERAGE "
             ."VDEF:max_speed=gpu_sm_max_speed,MAXIMUM "
             ."CDEF:dash_value=temp,POP,TIME,$range,%,$mod,LE,temp,UNKN,IF "
             ."LINE2:dash_value#FF0000:'MAX Limit=' "
             ."GPRINT:max_speed:'%6.2lf MHz' "
             ."TEXTALIGN:left "
             ."LINE2:'gpu_sm_clock'#555555:'GPU".$dIndex." SM Clock' "
             ."CDEF:user_pos=gpu_sm_clock,0,INF,LIMIT "
                . "VDEF:user_last=user_pos,LAST "
                . "VDEF:user_min=user_pos,MINIMUM "
                . "VDEF:user_avg=user_pos,AVERAGE "
                . "VDEF:user_max=user_pos,MAXIMUM "
                . "GPRINT:'user_last':'  ${space1}Now\:%5.0lf' "
                . "GPRINT:'user_min':'${space1}Min\:%5.0lf' "
                . "GPRINT:'user_avg':' ${space2}Avg\:%5.0lf' "
                . "GPRINT:'user_max':'${space1}Max\:%5.0lf\\l' ";

             

    $rrdtool_graph['series'] = $series;

    return $rrdtool_graph;

}

