package com.devwithguru.cricket.data.db.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import androidx.room.Update
import com.devwithguru.cricket.data.db.entity.BatterStatsEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface BatterStatsDao {
    @Query("SELECT * FROM batter_stats WHERE inningsId = :inningsId")
    fun getBattersByInnings(inningsId: Long): Flow<List<BatterStatsEntity>>

    @Query("SELECT * FROM batter_stats WHERE inningsId = :inningsId")
    suspend fun getBattersByInningsSync(inningsId: Long): List<BatterStatsEntity>

    @Query("SELECT * FROM batter_stats WHERE inningsId = :inningsId AND name = :name")
    suspend fun getBatter(inningsId: Long, name: String): BatterStatsEntity?

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertBatter(batter: BatterStatsEntity): Long

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAll(batters: List<BatterStatsEntity>)

    @Update
    suspend fun updateBatter(batter: BatterStatsEntity)

    @Query("DELETE FROM batter_stats WHERE inningsId = :inningsId")
    suspend fun deleteAllByInnings(inningsId: Long)
}
